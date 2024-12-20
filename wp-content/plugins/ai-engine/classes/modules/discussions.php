<?php

class Meow_MWAI_Modules_Discussions {
  private $wpdb = null;
  private $core = null;
  public $table_chats = null;
  private $db_check = false;
  private $namespace_admin = 'mwai/v1';
  private $namespace_ui = 'mwai-ui/v1';

  public function __construct(  ) {
    global $wpdb;
    $this->wpdb = $wpdb;
    global $mwai_core;
    $this->core = $mwai_core;
    $this->table_chats = $wpdb->prefix . 'mwai_chats';

    if ( $this->core->get_option( 'chatbot_discussions' ) ) {
      add_filter( 'mwai_chatbot_reply', [ $this, 'chatbot_reply' ], 10, 4 );
      add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );

      // TEMPORARY:
      $timestamp = wp_next_scheduled( 'mwai_discussions' );
      if ( $timestamp ) {
          wp_unschedule_event( $timestamp, 'mwai_discussions' );
      }
      if ( !wp_next_scheduled( 'mwai_discussions' ) ) {
        wp_schedule_event( time(), 'hourly', 'mwai_discussions' );
      }
      add_action( 'mwai_discussions', [ $this, 'cron_discussions' ] );
    }
  }

  public function rest_api_init() {

    // Admin
		register_rest_route( $this->namespace_admin, '/discussions/list', [
			'methods' => 'POST',
			'callback' => [ $this, 'rest_discussions_list' ],
			'permission_callback' => [ $this->core, 'can_access_settings' ],
		] );
    register_rest_route( $this->namespace_admin, '/discussions/delete', [
      'methods' => 'POST',
      'callback' => [ $this, 'rest_discussions_delete' ],
      'permission_callback' => [ $this->core, 'can_access_settings' ],
    ] );

    // UI
    register_rest_route( $this->namespace_ui, '/discussions/list', [
			'methods' => 'POST',
			'callback' => [ $this, 'rest_discussions_ui_list' ],
			'permission_callback' => '__return_true'
		] );
    register_rest_route( $this->namespace_ui, '/discussions/edit', [
      'methods' => 'POST',
      'callback' => [ $this, 'rest_discussions_ui_edit' ],
      'permission_callback' => '__return_true'
    ] );
    register_rest_route( $this->namespace_ui, '/discussions/delete', [
      'methods' => 'POST',
      'callback' => [ $this, 'rest_discussions_delete' ],
      'permission_callback' => [ $this, 'can_delete_discussion' ],
    ] );
	}

  function can_delete_discussion( $request ) {
    $params = $request->get_json_params();
    $chatIds = isset( $params['chatIds'] ) ? $params['chatIds'] : null;
    $userId = get_current_user_id();
    if ( !$userId ) {
      return false;
    }
    foreach ( $chatIds as $chatId ) {
      $chat = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT *
        FROM $this->table_chats
        WHERE chatId = %s", $chatId
        )
      );
      if ( !$chat || (int)$chat->userId !== (int)$userId ) {
        return false;
      }
    }
    return true;
  }

  function rest_discussions_list( $request ) {
		try {
			$params = $request->get_json_params();
			$offset = $params['offset'];
			$limit = $params['limit'];
      $filters = $params['filters'];
			$sort = $params['sort'];
			$chats = $this->chats_query( [], $offset, $limit, $filters, $sort );
			return new WP_REST_Response([ 'success' => true, 'total' => $chats['total'], 'chats' => $chats['rows'] ], 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

  function rest_discussions_ui_edit( $request ) {
    try {
      $params = $request->get_json_params();
      $chatId = isset( $params['chatId'] ) ? sanitize_text_field( $params['chatId'] ) : null;
      $title = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : null;
  
      if ( is_null( $chatId ) || is_null( $title ) ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'chatId and title are required.' ], 400 );
      }
  
      $userId = get_current_user_id();
      if ( ! $userId ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'You need to be logged in.' ], 401 );
      }
  
      // Update the discussion title for the current user
      $updated = $this->wpdb->update(
        $this->table_chats,
        [ 'title' => $title ],
        [ 'chatId' => $chatId, 'userId' => $userId ]
      );
  
      if ( $updated === false ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to update the discussion.' ], 500 );
      }
  
      return new WP_REST_Response( [ 'success' => true ], 200 );
    } catch ( Exception $e ) {
      return new WP_REST_Response( [ 'success' => false, 'message' => $e->getMessage() ], 500 );
    }
  }  

  function cron_discussions() {
    $this->check_db();
    $now = date( 'Y-m-d H:i:s' );
    $ten_days_ago = date( 'Y-m-d H:i:s', strtotime( '-10 days' ) );
  
    // Get 5 latest discussions, not older than 10 days, which have no 'title' yet (NULL)
    $query = $this->wpdb->prepare(
      "SELECT * FROM {$this->table_chats}
       WHERE title IS NULL AND updated >= %s
       ORDER BY updated DESC LIMIT 5",
      $ten_days_ago
    );
    $discussions = $this->wpdb->get_results( $query );
  
    if ( empty( $discussions ) ) {
      return;
    }
  
    foreach ( $discussions as $discussion ) {
      $messages = json_decode( $discussion->messages, true );
      if ( ! is_array( $messages ) ) {
        continue;
      }
  
      $has_user_message = false;
      $has_assistant_message = false;
  
      // Check for at least one message from 'user' and one from 'assistant'
      foreach ( $messages as $message ) {
        if ( isset( $message['role'] ) ) {
          if ( $message['role'] === 'user' ) {
            $has_user_message = true;
          }
          if ( $message['role'] === 'assistant' ) {
            $has_assistant_message = true;
          }
        }
        if ( $has_user_message && $has_assistant_message ) {
          break;
        }
      }
  
      if ( ! ( $has_user_message && $has_assistant_message ) ) {
        continue;
      }
  
      // Prepare the conversation text for the prompt
      $conversation_text = '';
      foreach ( $messages as $message ) {
        if ( isset( $message['role'] ) && isset( $message['content'] ) ) {
          $role = ucfirst( $message['role'] );
          $content = $message['content'];
          $conversation_text .= "$role: $content\n";
        }
      }
  
      $base_prompt = "Based on the following conversation, generate a concise and specific title for the discussion, less than 64 characters. Focus on the main topic, avoiding unnecessary words such as articles, pronouns, or adjectives. Do not include any punctuation at the end. Do not include anything else than the title itself, only one sentence, no line breaks, just the title.\n\nConversation:\n$conversation_text\n";
      $prompt = apply_filters( 'mwai_discussions_title_prompt', $base_prompt, $conversation_text, $discussion );
  
      // Run the AI query
      global $mwai;
      $answer = $mwai->simpleTextQuery( $prompt, [ "scope" => 'discussions' ] );
  
      // Clean up the answer
      $title = trim( $answer );
      $title = rtrim( $title, ".!?:;,—–-–" ); // Remove trailing punctuation
      $title = substr( $title, 0, 100 ); // Ensure less than 100 characters
  
      // If the AI returns a title, update the discussion with it
      if ( ! empty( $title ) ) {
        $this->wpdb->update(
          $this->table_chats,
          [ 'title' => $title ],
          [ 'id' => $discussion->id ]
        );
      } else {
        // If the AI doesn't return a title, update the discussion with "Untitled"
        $this->wpdb->update(
          $this->table_chats,
          [ 'title' => 'Untitled' ],
          [ 'id' => $discussion->id ]
        );
      }
    }
  }  

  function rest_discussions_ui_list( $request ) {
		try {
			$params = $request->get_json_params();
			$offset = isset( $params['offset'] ) ? $params['offset'] : 0;
			$limit = isset( $params['limit'] ) ? $params['limit'] : 10;
      $botId = isset( $params['botId'] ) ? $params['botId'] : null;
      $customId = isset( $params['customId'] ) ? $params['customId'] : null;

      if ( !is_null( $customId ) ) {
        $botId = $customId;
      }

      if ( is_null( $botId ) ) {
        return new WP_REST_Response([ 'success' => false, 'message' => "Bot ID is required." ], 200 );
      }

      $userId = get_current_user_id();
      if ( !$userId ) {
        return new WP_REST_Response([ 'success' => false, 'message' => "You need to be connected." ], 200 );
      }
			$filters = [ 
        [ 'accessor' => 'user', 'value' => $userId ],
        [ 'accessor' => 'botId', 'value' => $botId ],
      ];
			$chats = $this->chats_query( [], $offset, $limit, $filters );
			return new WP_REST_Response([ 'success' => true, 'total' => $chats['total'], 'chats' => $chats['rows'] ], 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

  function rest_discussions_delete( $request ) {
    try {
      $params = $request->get_json_params();
      $chatIds = isset( $params['chatIds'] ) ? $params['chatIds'] : null;
  
      if ( ! is_array( $chatIds ) || empty( $chatIds ) ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'chatIds is required.' ], 400 );
      }
  
      $userId = get_current_user_id();
      if ( ! $userId ) {
        return new WP_REST_Response( [ 'success' => false, 'message' => 'You need to be logged in.' ], 401 );
      }
  
      foreach ( $chatIds as $chatId ) {
        $this->wpdb->delete( $this->table_chats, [ 'chatId' => $chatId, 'userId' => $userId ] );
      }
  
      return new WP_REST_Response( [ 'success' => true ], 200 );
    } catch ( Exception $e ) {
      return new WP_REST_Response( [ 'success' => false, 'message' => $e->getMessage() ], 500 );
    }
  }  
  
  // Get latest discussion for the given parameter
  function get_discussion( $botId, $chatId ) {
    $this->check_db();
    $chat = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT *
      FROM $this->table_chats
      WHERE chatId = %s AND botId = %s", $chatId, $botId
    ), ARRAY_A );
    if ( $chat ) {
      $chat['messages'] = json_decode( $chat['messages'] );
      return $chat;
    }
    return null;
  }

  function chats_query( $chats = [], $offset = 0, $limit = null, $filters = null, $sort = null ) {
    $this->check_db();
    $offset = !empty( $offset ) ? intval( $offset ) : 0;
    $limit = !empty( $limit ) ? intval( $limit ) : 5;
    $filters = !empty( $filters ) ? $filters : [];
    $this->core->sanitize_sort( $sort, 'updated', 'DESC' );
  
    $where_clauses = [];
    $where_values = [];
  
    if ( is_array( $filters ) ) {
      foreach ( $filters as $filter ) {
        $value = $filter['value'];
        if ( is_null( $value ) || $value === '' ) {
          continue;
        }
  
        switch ( $filter['accessor'] ) {
          case 'user':
            $isIP = filter_var( $value, FILTER_VALIDATE_IP );
            if ( $isIP ) {
              $where_clauses[] = 'ip = %s';
              $where_values[] = $value;
            } else {
              $where_clauses[] = 'userId = %d';
              $where_values[] = intval( $value );
            }
            break;
  
          case 'botId':
            $where_clauses[] = 'botId = %s';
            $where_values[] = $value;
            break;
  
          case 'preview':
            $like = '%' . $this->wpdb->esc_like( $value ) . '%';
            $where_clauses[] = 'messages LIKE %s';
            $where_values[] = $like;
            break;
  
          // Add other cases as needed
        }
      }
    }
  
    $where_sql = '';
    if ( !empty( $where_clauses ) ) {
      $where_sql = 'WHERE ' . implode( ' AND ', $where_clauses );
    }
  
    $order_by = 'ORDER BY ' . esc_sql( $sort['accessor'] ) . ' ' . esc_sql( $sort['by'] );
  
    $limit_sql = '';
    if ( $limit > 0 ) {
      $limit_sql = $this->wpdb->prepare( 'LIMIT %d, %d', $offset, $limit );
    }
  
    $query = "SELECT * FROM {$this->table_chats} {$where_sql} {$order_by} {$limit_sql}";
  
    // Execute the prepared statement
    $chats['rows'] = $this->wpdb->get_results( $this->wpdb->prepare( $query, $where_values ), ARRAY_A );
  
    // Get the total count
    $count_query = "SELECT COUNT(*) FROM {$this->table_chats} {$where_sql}";
    $chats['total'] = $this->wpdb->get_var( $this->wpdb->prepare( $count_query, $where_values ) );
  
    return $chats;
  }
  
  public function chatbot_reply( $rawText, $query, $params, $extra ) {
    global $mwai_core;
    $userIp = $mwai_core->get_ip_address();
    $userId = $mwai_core->get_user_id();
    $botId = isset( $params['botId'] ) ? $params['botId'] : null;
    $chatId = $this->core->fix_chat_id( $query, $params );
    $customId = isset( $params['customId'] ) ? $params['customId'] : null;
    $threadId = $query instanceof Meow_MWAI_Query_Assistant ? $query->threadId : null;
    $storeId = $query instanceof Meow_MWAI_Query_Assistant ? $query->storeId : null;
    $now = date( 'Y-m-d H:i:s' );

    if ( !empty( $customId ) ) {
      $botId = $customId;
    }
    $newMessage = isset( $params['newMessage'] ) ? $params['newMessage'] : $query->get_message();

    // If there is a file for "Vision", add it to the message
    if ( isset( $query->filePurpose ) && $query->filePurpose === 'vision' && isset( $query->file ) ) {
      $newMessage = "![Uploaded Image]({$query->file})\n" . $newMessage;
    }

    //$chatId = hash( 'sha256', $userIp . $userId . $clientChatId );
    $this->check_db();
    $chat = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT *
      FROM $this->table_chats
      WHERE chatId = %s", $chatId
      )
    );
    $messageExtra = [
      'embeddings' => isset( $extra['embeddings'] ) ? $extra['embeddings'] : null
    ];
    $chatExtra = [
      'session' => $query->session,
      'model' => $query->model,
    ];
    if ( !empty( $query->temperature ) ) {
      $chatExtra['temperature'] = $query->temperature;
    }
    if ( !empty( $query->context ) ) {
      $chatExtra['context'] = $query->context;
    }
    if ( $query instanceof Meow_MWAI_Query_Assistant ) {
      $chatExtra['assistantId'] = $query->assistantId;
      $chatExtra['threadId'] = $query->threadId;
      $chatExtra['storeId'] = $query->storeId;
    }
    if ( $chat ) {
      $chat->messages = json_decode( $chat->messages );
      $chat->messages[] = [ 'role' => 'user', 'content' => $newMessage ];
      $chat->messages[] = [ 'role' => 'assistant', 'content' => $rawText, 'extra' => $messageExtra ];
      $chat->messages = json_encode( $chat->messages );
      $this->wpdb->update( $this->table_chats, [ 
        'userId' => $userId,
        'messages' => $chat->messages,
        'updated' => $now
       ], [ 'id' => $chat->id ] );
    }
    else {
      $chat = [
        'userId' => $userId,
        'ip' => $userIp,
        'messages' => json_encode( [ 
          [ 'role' => 'user', 'content' => $newMessage ],
          [ 'role' => 'assistant', 'content' => $rawText, 'extra' => $messageExtra ]
        ] ),
        'extra' => json_encode( $chatExtra ),
        'botId' => $botId,
        'chatId' => $chatId,
        'threadId' => $threadId,
        'storeId' => $storeId,
        'created' => $now,
        'updated' => $now
      ];
      $this->wpdb->insert( $this->table_chats, $chat );
    }
    return $rawText;
  }

  function format_messages( $json, $format = 'html' ) {
    $html = '';
    if ( $format === 'html' ) {
      try {
        $conversation = json_decode( $json, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
          return 'Invalid JSON format';
        }
        foreach ( $conversation as $message ) {
          $role = ucfirst( $message['role'] );
          $html .= '<p><strong>' . htmlspecialchars( $role ) . ':</strong> ' . htmlspecialchars( $message['content'] ) . '</p>';
        }
      }
      catch ( Exception $e ) {
        error_log( $e->getMessage() );
        return 'Error while formatting the message';
      }
    }
    $html = apply_filters( 'mwai_discussion_format_messages', $html, $json, $format );
    return $html;
  }  

  function check_db() {
    if ( $this->db_check ) {
      return true;
    }
    $this->db_check = !( strtolower( 
      $this->wpdb->get_var( "SHOW TABLES LIKE '$this->table_chats'" ) ) != strtolower( $this->table_chats )
    );
    if ( !$this->db_check ) {
      $this->create_db();
      $this->db_check = !( strtolower( 
        $this->wpdb->get_var( "SHOW TABLES LIKE '$this->table_chats'" ) ) != strtolower( $this->table_chats )
      );
    }

    // LATER: REMOVE THIS AFTER MARCH 2025
    $this->db_check = $this->db_check && $this->wpdb->get_var( "SHOW COLUMNS FROM $this->table_chats LIKE 'title'" );
    if ( !$this->db_check ) {
      $this->wpdb->query( "ALTER TABLE $this->table_chats ADD COLUMN title VARCHAR(64) NULL" );
      $this->db_check = true;
    }

    return $this->db_check;
  }

  function create_db() {
    $charset_collate = $this->wpdb->get_charset_collate();
    $sqlLogs = "CREATE TABLE $this->table_chats (
      id BIGINT(20) NOT NULL AUTO_INCREMENT,
      userId BIGINT(20) NULL,
      ip VARCHAR(64) NULL,
      title VARCHAR(64) NULL,
      messages TEXT NOT NULL NULL,
      extra LONGTEXT NOT NULL NULL,
      botId VARCHAR(64) NULL,
      chatId VARCHAR(64) NOT NULL,
      threadId VARCHAR(64) NULL,
      storeId VARCHAR(64) NULL,
      created DATETIME NOT NULL,
      updated DATETIME NOT NULL,
      PRIMARY KEY  (id),
      INDEX chatId (chatId)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sqlLogs );
  }

}