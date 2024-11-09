<?php
namespace TrxAddons\AiHelper;

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\StableDiffusion;
use TrxAddons\AiHelper\StabilityAi;

if ( ! class_exists( 'Utils' ) ) {

	/**
	 * Utilities for text and image generation
	 */
	class Utils {

		static $cache_time = 10 * 60;	// Cache time (in seconds)
		static $cache_name = 'trx_addons_ai_helper_cache';	// Cache name

		/**
		 * Save a cache data
		 * 
		 * @param array  Cache data
		 * 
		 * @return void
		 */
		static function set_cache_data( $cache ) {
			set_transient( self::$cache_name, $cache, self::$cache_time );
		}

		/**
		 * Return a cache data
		 * 
		 * @return array  Cache data
		 */
		static function get_cache_data() {
			$cache = get_transient( self::$cache_name, array() );
			return self::clear_expired_cache_data( is_array( $cache ) ? $cache : array() );
		}

		/**
		 * Clear expired data from the cache
		 * 
		 * @param array $cache  Cache data
		 * 
		 * @return array  Cache data
		 */
		static function clear_expired_cache_data( $cache ) {
			if ( ! empty( $cache ) && is_array( $cache ) ) {
				foreach( $cache as $id => $data ) {
					if ( ! empty( $data['time'] ) && $data['time'] + self::$cache_time < time() ) {
						unset( $cache[ $id ] );
					}
				}
			}
			return $cache;
		}


		/**
		 * Save a value for the specified id to the cache for the future use
		 * 
		 * @param string $id     Cache id
		 * @param string $value  Value to save
		 * 
		 * @return void
		 */
		static function save_data_to_cache( $id, $value ) {
			$cache = self::get_cache_data();
			$cache[ $id ] = array( 'value' => $value, 'time' => time() );
			self::set_cache_data( $cache );
		}

		/**
		 * Return an entry value for the specified id from the cache if exists
		 * 
		 * @param string $id  Cache id
		 * 
		 * @return string  Entry value saved with a specified id or empty string
		 */
		static function get_data_from_cache( $id ) {
			$cache = self::get_cache_data();
			return isset( $cache[ $id ]['value'] ) ? $cache[ $id ]['value'] : '';
		}

		/**
		 * Delete a cache entry with the specified id from the cache
		 * 
		 * @param string $id  Cache id
		 * 
		 * @return void
		 */
		static function delete_data_from_cache( $id ) {
			$cache = self::get_cache_data();
			if ( isset( $cache[ $id ] ) ) {
				unset( $cache[ $id ] );
				self::set_cache_data( $cache );
			}
		}

		/**
		 * Parse the response and return an array with answer
		 * 
		 * @param string $response  Response from the API
		 * @param string $model     Image model
		 * @param array  $answer    Array with answer's start data
		 * @param string $mode      Optional. Mode of the generation (image, music, audio)
		 * 
		 * @return array  Array with answer
		 */
		static function parse_response( $response, $model, $answer, $mode = 'image' ) {

			if ( $mode == 'image' ) {

				// ModelsLab (ex Stable Diffusion) API response
				if ( self::is_stable_diffusion_model( $model ) ) {
					if ( ! empty( $response['status'] ) ) {
						if ( $response['status'] == 'success' ) {
							if ( ! empty( $response['output'] ) && is_array( $response['output'] ) && ! empty( $response['output'][0] ) ) {
								foreach( $response['output'] as $image_url ) {
									$answer['data']['images'][] = array(
										'url' => $image_url,
									);
									// Save a file name and url to the cache
									self::save_data_to_cache( trx_addons_get_file_name( $image_url, false ), $image_url );
								}
							} else {
								$answer['error'] = __( 'Error! Unexpected answer from the ModelsLab (ex Stable Diffusion) Image API.', 'trx_addons' );
							}
						} else if ( $response['status'] == 'processing' ) {
							if ( ! empty( $response['fetch_result'] ) ) {
								$parts = explode( '/api/', $response['fetch_result'] );
								$answer['data'] = array_merge( $answer['data'], apply_filters( 'trx_addons_filter_ai_helper_fetch', array(
									'fetch_model' => $model,
									'fetch_id'  => $response['id'],
									'fetch_url' => ! empty( $parts[1] ) ? $parts[1] : '',
									'fetch_img' => trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/fetch.png' ),
									'fetch_msg' => __( 'wait for render...', 'trx_addons' ),
									'fetch_time' => apply_filters( 'trx_addons_filter_sc_igenerator_fetch_time', 8000 ),
								) ) );
								// Save id to the cache
								self::save_data_to_cache( $response['id'], $model );
							}
						} else if ( in_array( $response['status'], array( 'error', 'failed' ) ) ) {
							if ( ! empty( $response['message'] ) ) {
								$answer['error'] = is_array( $response['message'] ) ? join( ', ', trx_addons_array_get_values( $response['message'] ) ) : $response['message'];
							} else if ( ! empty( $response['messege'] ) ) {
								$answer['error'] = is_array( $response['messege'] ) ? join( ', ', trx_addons_array_get_values( $response['messege'] ) ) : $response['messege'];
							} else {
								$answer['error'] = __( 'Error! Undefined response from the ModelsLab (ex Stable Diffusion) Image API.', 'trx_addons' );
							}
							if ( ! empty( $response['error_log']['response']['message'] ) ) {
								$answer['error'] .= ' ' . $response['error_log']['response']['message'];
							} else if ( ! empty( $response['response']['message'] ) ) {
								$answer['error'] .= ' ' . $response['response']['message'];
							}
						} else {
							$answer['error'] = __( 'Error! Unexpected response from the ModelsLab (ex Stable Diffusion) Image API.', 'trx_addons' );
						}
					} else {
						$answer['error'] = __( 'Error! Unknown response from the ModelsLab (ex Stable Diffusion) Image API.', 'trx_addons' );
					}

				// Stability AI API response
				} else if ( self::is_stability_ai_model( $model ) ) {
					if ( ! empty( $response['artifacts'] ) ) {
						if ( is_array( $response['artifacts'] ) && ! empty( $response['artifacts'][0]['base64'] ) ) {
							foreach( $response['artifacts'] as $data ) {
								if ( empty( $data['base64'] ) || empty( $data['finishReason'] ) || ! in_array( $data['finishReason'], apply_filters( 'trx_addons_filter_ai_helper_stability_finish_reasons', array( 'SUCCESS', 'CONTENT_FILTERED' ) ) ) ) {
									continue;
								}
								$image_url = trx_addons_uploads_save_data( base64_decode( $data['base64'] ), array(
									'expire' => apply_filters( 'trx_addons_filter_ai_helper_stability_expire_time', self::$cache_time ),
								) );
								$answer['data']['images'][] = array(
									'url' => $image_url,
								);
								// Save a file name and url to the cache
								self::save_data_to_cache( trx_addons_get_file_name( $image_url, false ), $image_url );
							}
						} else {
							$answer['error'] = __( 'Error! Unexpected answer from the Stability AI API.', 'trx_addons' );
						}
					} else if ( ! empty( $response['message'] ) ) {
						$answer['error'] = $response['message'];
					} else {
						$answer['error'] = __( 'Error! Unknown response from the Stability AI API.', 'trx_addons' );
					}

				// OpenAi API response
				} else if ( self::is_openai_model( $model ) ) {
					if ( ! empty( $response['data'] ) && ! empty( $response['data'][0]['url'] ) ) {
						$answer['data']['images'] = $response['data'];
						// Save a file name and url to the cache
						foreach( $response['data'] as $image_data ) {
							if ( ! empty( $image_data['url'] ) ) {
								self::save_data_to_cache( trx_addons_get_file_name( $image_data['url'], false ), $image_data['url'] );
							}
						}
					} else {
						if ( ! empty( $response['error']['message'] ) ) {
							$answer['error'] = $response['error']['message'];
						} else {
							$answer['error'] = __( 'Error! Unknown response from the API. Maybe the API server is not available right now.', 'trx_addons' );
						}
					}
				}

			} else if ( $mode == 'music' || $mode == 'audio' ) {

				// ModelsLab or Open AI Audio API response for actions 'Generate music' and 'Generate audio'
				if ( ! empty( $response['status'] ) ) {
					if ( $response['status'] == 'success' ) {
						// ModelsLab API response for the action "Generate music" or "Generate audio"
						if ( ! empty( $response['output'] ) && is_array( $response['output'] ) && ! empty( $response['output'][0] ) ) {
							foreach( $response['output'] as $url ) {
								if ( trx_addons_get_file_ext( $url ) == 'txt' ) {
									$answer['data']['text'] = trim( trx_addons_fgc( $url ) );
									if ( is_string( $answer['data']['text'] ) && substr( $answer['data']['text'], 0, 1 ) == '[' && substr( $answer['data']['text'], -1 ) == ']' ) {
										$json = json_decode( $answer['data']['text'], true );
										if ( is_array( $json ) && ! empty( $json[0]['text'] ) ) {
											$answer['data'][ 'text' ] = $json[0]['text'];
										}
									}
								} else {
									$answer['data'][ $mode ][] = array(
										'url' => $url,
									);
									// Save a file name and url to the cache
									self::save_data_to_cache( trx_addons_get_file_name( $url, false ), $url );
								}
							}
						// ModelsLab or Open AI Audio API response for actions 'Transcription' and 'Translation'
						} else if ( ! empty( $response['text'] ) ) {
							$answer['data']['text'] = $response['text'];
						// Unexpected response
						} else {
							$answer['error'] = __( 'Error! Unexpected answer from the Audio API.', 'trx_addons' );
						}
					} else if ( $response['status'] == 'processing' ) {
						if ( ! empty( $response['fetch_result'] ) ) {
							$parts = explode( '/api/', $response['fetch_result'] );
							$answer['data'] = array_merge( $answer['data'], apply_filters( 'trx_addons_filter_ai_helper_fetch', array(
								'fetch_model' => $model,
								'fetch_id'  => $response['id'],
								'fetch_url' => ! empty( $parts[1] ) ? $parts[1] : '',
								'fetch_msg' => __( 'wait for generation...', 'trx_addons' ),
								'fetch_time' => apply_filters( 'trx_addons_filter_sc_agenerator_fetch_time', 8000 )
							) ) );
							// Save id to the cache
							self::save_data_to_cache( $response['id'], $model );
						}
					} else if ( in_array( $response['status'], array( 'error', 'failed' ) ) ) {
						if ( ! empty( $response['message'] ) ) {
							$answer['error'] = is_array( $response['message'] ) ? join( ', ', trx_addons_array_get_values( $response['message'] ) ) : $response['message'];
						} else if ( ! empty( $response['messege'] ) ) {
							$answer['error'] = is_array( $response['messege'] ) ? join( ', ', trx_addons_array_get_values( $response['messege'] ) ) : $response['messege'];
						} else {
							$answer['error'] = __( 'Error! Undefined response from the Audio API.', 'trx_addons' );
						}
						if ( ! empty( $response['error_log']['response']['message'] ) ) {
							$answer['error'] .= ' ' . $response['error_log']['response']['message'];
						} else if ( ! empty( $response['response']['message'] ) ) {
							$answer['error'] .= ' ' . $response['response']['message'];
						}
					} else {
						$answer['error'] = __( 'Error! Unexpected response from the Audio API.', 'trx_addons' );
					}

				} else {
					$answer['error'] = __( 'Error! Unknown response from the Audio API.', 'trx_addons' );
				}

			}
			return $answer;
		}

		/**
		 * Return a default value of maximum tokens
		 * 
		 * @return int  Default value of maximum tokens
		 */
		static function get_default_max_tokens() {
			return apply_filters( 'trx_addons_filter_ai_helper_default_max_tokens', 128000 );
		}

		/**
		 * Return a maximum tokens count for all available API
		 * 
		 * @return int  Max tokens count
		 */
		static function get_max_tokens( $sc = 'sc_chat' ) {
			$max_tokens = 0;
			$api_order = trx_addons_get_option( "ai_helper_{$sc}_api_order", Lists::get_list_ai_chat_apis_enabled() );
			foreach( $api_order as $api => $enable ) {
				if ( $api == 'openai' && (int)$enable > 0 ) {
					$max_tokens = max( $max_tokens, OpenAi::get_max_tokens( '*' ) );
				} else if ( $api == 'openai-assistants' && (int)$enable > 0 ) {
					$max_tokens = max( $max_tokens, OpenAiAssistants::get_max_tokens( '*' ) );
				} else if ( $api == 'flowise-ai' && (int)$enable > 0 ) {
					$max_tokens = max( $max_tokens, FlowiseAi::get_max_tokens( '*' ) );
				} else if ( $api == 'google-ai' && (int)$enable > 0 ) {
					$max_tokens = max( $max_tokens, GoogleAi::get_max_tokens( '*' ) );
				}
			}
			return apply_filters( 'trx_addons_filter_ai_helper_max_tokens', $max_tokens > 0 ? $max_tokens : self::get_default_max_tokens(), $sc );
		}

		/**
		 * Return an API object for the specified chat model
		 * 
		 * @param string $model  Chat model
		 * 
		 * @return object  API object
		 */
		static function get_chat_api( $model = '' ) {
			if ( empty( $model ) ) {
				$model = trx_addons_get_option( 'ai_helper_text_model_default', 'openai/default' );
			}
			if ( self::is_flowise_ai_model( $model ) ) {
				return FlowiseAi::instance();
			} else if ( self::is_google_ai_model( $model ) ) {
				return GoogleAi::instance();
			} else if ( self::is_openai_assistants_model( $model ) ) {
				return OpenAiAssistants::instance();
			} else if ( self::is_trx_ai_assistants_model( $model ) ) {
				return TrxAiAssistants::instance();
			} else {
				return OpenAi::instance();
			}
		}

		/**
		 * Return an API object for the specified image model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return object  API object
		 */
		static function get_image_api( $model ) {
			if ( self::is_stable_diffusion_model( $model ) ) {
				return StableDiffusion::instance();
			} else if ( self::is_stability_ai_model( $model ) ) {
				return StabilityAi::instance();
			} else {
				return OpenAi::instance();
			}
		}

		/**
		 * Return an API object for the specified music model
		 * 
		 * @param string $model  Optional. Music model
		 * 
		 * @return object  API object
		 */
		static function get_music_api( $model = '' ) {
			return ModelsLab::instance();
		}

		/**
		 * Return default music model for the 'Generate music' button
		 * 
		 * @return string  Music model
		 */
		static function get_default_music_model() {
			return 'modelslab/music-generator';
		}

		/**
		 * Return an API object for the specified audio model
		 * 
		 * @param string $model  Audio model
		 * 
		 * @return object  API object
		 */
		static function get_audio_api( $model ) {
			if ( self::is_modelslab_model( $model ) ) {
				return ModelsLab::instance();
			} else {
				return OpenAi::instance();
			}
		}

		/**
		 * Return default audio model for the 'Generate audio' button
		 * 
		 * @return string  audio model
		 */
		static function get_default_audio_model( $type = 'tts') {
			return $type == 'tts' ? 'openai/tts-1' : 'openai/whisper-1';
		}

		/**
		 * Return default image model for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return string  Image model
		 */
		static function get_default_image_model() {
			return 'openai/default';
		}

		/**
		 * Return default image size for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return string  Image size in format 'WxH'
		 */
		static function get_default_image_size( $mode = 'media' ) {
			return $mode == 'media' ? '1024x1024' : '256x256';
		}

		/**
		 * Check if the image is in the list of allowed sizes
		 * 
		 * @return string  Image size in format 'WxH'
		 */
		static function check_image_size( $size, $mode = 'media' ) {
			$allowed_sizes = Lists::get_list_ai_image_sizes();
			return ! empty( $allowed_sizes[ $size ] ) ? $size : self::get_default_image_size( $mode );
		}

		/**
		 * Return a maximum width of the image for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return int  Max width in pixels
		 */
		static function get_max_image_width( $model = '' ) {
			return apply_filters( 'trx_addons_filter_ai_helper_max_image_width',
					empty( $model )
					? 1024
					: ( self::is_stability_ai_model( $model )
						? 1536
						: ( self::is_openai_dall_e_3_model( $model )
							? 1792
							: 1024
							)
						),
					$model
				);
		}

		/**
		 * Return a maximum height of the image for the 'Generate images' button and the 'Make variations' button
		 * 
		 * @return int  Max height in pixels
		 */
		static function get_max_image_height( $model = '' ) {
			return apply_filters( 'trx_addons_filter_ai_helper_max_image_height',
					empty( $model )
						? 1024
						: ( self::is_stability_ai_model( $model )
							? 1536
							: ( self::is_openai_dall_e_3_model( $model )
								? 1792
								: 1024
								)
							),
					$model
				);
		}

		/**
		 * Return a language code (ISO 639-1, e.g., 'en', 'es', 'fr') for the specified language name
		 * (e.g., 'english', 'spanish', 'french')
		 * 
		 * @param string $language  Language name
		 * 
		 * @return string  Language code
		 */
		static function language_to_iso( $language ) {
			$language = strtolower( $language );
			$languages = array(
				'english' => 'en',
				'spanish' => 'es',
				'french' => 'fr',
				'german' => 'de',
				'italian' => 'it',
				'portuguese' => 'pt',
				'dutch' => 'nl',
				'polish' => 'pl',
				'turkish' => 'tr',
				'japanese' => 'ja',
				'korean' => 'ko',
				'chinese' => 'zh',
				'arabic' => 'ar',
				'brazilian' => 'pt',
				'hindi' => 'hi',
				'hungarian' => 'hu',
				'ukrainian' => 'uk',
				'russian' => 'ru',
			);
			return ! empty( $languages[ $language ] ) ? $languages[ $language ] : 'en';
		}

		/**
		 * Check if the model is support an image style presets
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model support an image style presets
		 */
		static function is_model_support_image_style( $model ) {
			return self::is_stability_ai_model( $model ) || self::is_openai_dall_e_3_model( $model );
		}

		/**
		 * Check if the model is support separate image dimensions 'width' and 'height' or only 'size'
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model support separate image dimensions 'width' and 'height'
		 */
		static function is_model_support_image_dimensions( $model ) {
			return self::is_stable_diffusion_model( $model ) || self::is_stability_ai_model( $model );
		}

		/**
		 * Check if the model is support a negative prompt
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model support a negative prompt
		 */
		static function is_model_support_negative_prompt( $model ) {
			return self::is_stable_diffusion_model( $model ) || self::is_stability_ai_model( $model );
		}

		/**
		 * Check if the model is support emotions
		 * 
		 * @param string $model  Audio model
		 * 
		 * @return bool  True if the model support emotions
		 */
		static function is_model_support_emotions( $model ) {
			return self::is_modelslab_model( $model );
		}

		/**
		 * Check if the model is support a language
		 * 
		 * @param string $model  Audio model
		 * 
		 * @return bool  True if the model is support a language
		 */
		static function is_model_support_language( $model ) {
			return self::is_modelslab_model( $model );
		}

		/**
		 * Check if the model is support an init audio for generation
		 * 
		 * @param string $model  Audio model
		 * 
		 * @return bool  True if the model is support an init audio for generation
		 */
		static function is_model_support_init_audio( $model ) {
			return self::is_modelslab_model( $model );
		}

		/**
		 * Check if the model is support a base64 encoding
		 * 
		 * @param string $model  Audio model
		 * 
		 * @return bool  True if the model is support a base64 encoding
		 */
		static function is_model_support_base64( $model ) {
			return self::is_modelslab_model( $model ) && strpos( $model, 'speech-to-text' ) === false;
		}

		/**
		 * Check if the model is a ModelsLab model
		 * 
		 * @param string $model  Model name
		 * 
		 * @return bool  True if the model is a ModelsLab model
		 */
		static function is_modelslab_model( $model ) {
			return strpos( $model, 'modelslab/' ) !== false;
		}

		/**
		 * Check if the model is a Stable Diffusion model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a Stable Diffusion model
		 */
		static function is_stable_diffusion_model( $model ) {
			return strpos( $model, 'stable-diffusion/' ) !== false;
		}

		/**
		 * Check if the model is a Stability AI model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a Stability AI model
		 */
		static function is_stability_ai_model( $model ) {
			return strpos( $model, 'stability-ai/' ) !== false;
		}

		/**
		 * Check if the model is a OpenAi model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a OpenAi model
		 */
		static function is_openai_model( $model ) {
			return strpos( $model, 'openai/' ) !== false;
		}

		/**
		 * Check if the model is a Open Ai Assistant
		 * 
		 * @param string $model  Chat model
		 * 
		 * @return bool  True if the model is a Open Ai Assistant
		 */
		static function is_openai_assistants_model( $model ) {
			return strpos( $model, 'openai-assistants/' ) !== false;
		}

		/**
		 * Check if the model is a OpenAi image model
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a OpenAi image model
		 */
		static function is_openai_image_model( $model ) {
			return strpos( $model, 'openai/' ) !== false && ( $model == 'openai/default' || strpos( $model, 'dall-e' ) !== false );
		}

		/**
		 * Check if the model is a OpenAi model DALL-E-3
		 * 
		 * @param string $model  Image model
		 * 
		 * @return bool  True if the model is a OpenAi model DALL-E-3
		 */
		static function is_openai_dall_e_3_model( $model ) {
			return strpos( $model, 'openai/' ) !== false && strpos( $model, 'dall-e-3' ) !== false;
		}

		/**
		 * Check if the model is a Flowise Ai model
		 * 
		 * @param string $model  Chat model
		 * 
		 * @return bool  True if the model is a Flowise Ai model
		 */
		static function is_flowise_ai_model( $model ) {
			return strpos( $model, 'flowise-ai/' ) !== false;
		}

		/**
		 * Check if the model is a Google Ai model
		 * 
		 * @param string $model  Chat model
		 * 
		 * @return bool  True if the model is a Google Ai model
		 */
		static function is_google_ai_model( $model ) {
			return strpos( $model, 'google-ai/' ) !== false;
		}

		/**
		 * Check if the model is a ThemeRex Ai Assistant
		 * 
		 * @param string $model  Chat model
		 * 
		 * @return bool  True if the model is a ThemeRex Ai Assistant
		 */
		static function is_trx_ai_assistants_model( $model ) {
			return strpos( $model, 'trx-ai-assistants/ai-assistant' ) !== false;
		}

		/**
		 * Check if any AI API is available
		 * 
		 * @param string $sc  Shortcode name sc_chat | sc_tgenerator | sc_igenerator
		 * 
		 * @return bool  True if any AI API is available
		 */
		static function is_api_available( $sc ) {
			$rez = false;
			if ( $sc == 'sc_igenerator' ) {
				$default = Lists::get_list_ai_image_apis_enabled();
			} else if ( $sc == 'sc_mgenerator' ) {
				$default = Lists::get_list_ai_music_apis_enabled();
			} else if ( $sc == 'sc_agenerator' ) {
				$default = Lists::get_list_ai_audio_apis_enabled();
			} else {
				$default = Lists::get_list_ai_chat_apis_enabled();
			}
			$api_order = trx_addons_get_option( "ai_helper_{$sc}_api_order", $default );
			foreach( $api_order as $api => $enable ) {
				if ( (int)$enable > 0 && trx_addons_get_option( 'ai_helper_token_' . str_replace( array( '-assistants', '-' ), array( '', '_' ), $api ), '' ) != '' ) {
					$rez = true;
					break;
				}
			}
			return $rez;
		}

		/**
		 * Check if any text API is available
		 * 
		 * @return bool  True if any text API is available
		 */
		static function is_text_api_available() {
			return self::is_api_available( 'sc_tgenerator' );
		}

		/**
		 * Check if any chat API is available
		 * 
		 * @param string $model  Chat model (optional)
		 * 
		 * @return bool  True if any chat API is available
		 */
		static function is_chat_api_available( $model = '' ) {
			return self::is_trx_ai_assistants_model( $model ) || self::is_api_available( 'sc_chat' );
		}

		/**
		 * Check if any image API is available
		 * 
		 * @return bool  True if any image API is available
		 */
		static function is_image_api_available() {
			return self::is_api_available( 'sc_igenerator' );
		}

		/**
		 * Check if any music API is available
		 * 
		 * @return bool  True if any music API is available
		 */
		static function is_music_api_available() {
			return self::is_api_available( 'sc_mgenerator' );
		}

		/**
		 * Check if any audio API is available
		 * 
		 * @return bool  True if any audio API is available
		 */
		static function is_audio_api_available() {
			return self::is_api_available( 'sc_agenerator' );
		}
	}
}
