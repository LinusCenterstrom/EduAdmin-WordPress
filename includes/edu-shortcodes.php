<?php
defined( 'ABSPATH' ) || die( 'This plugin must be run within the scope of WordPress.' );
if ( ! function_exists( 'normalize_empty_atts' ) ) {
	function normalize_empty_atts( $atts ) {
		if ( empty( $atts ) ) {
			return $atts;
		}
		foreach ( $atts as $attribute => $value ) {
			if ( is_int( $attribute ) ) {
				$atts[ strtolower( $value ) ] = true;
				unset( $atts[ $attribute ] );
			}
		}

		return $atts;
	}
}

function eduadmin_get_list_view( $attributes ) {
	$t                 = EDU()->start_timer( __METHOD__ );
	$selected_template = get_option( 'eduadmin-listTemplate', 'template_A' );
	$attributes        = shortcode_atts(
		array(
			'template'        => $selected_template,
			'category'        => null,
			'subject'         => null,
			'subjectid'       => null,
			'hidesearch'      => false,
			'onlyevents'      => false,
			'onlyempty'       => false,
			'numberofevents'  => null,
			'mode'            => null,
			'orderby'         => null,
			'order'           => null,
			'showsearch'      => null,
			'showmore'        => null,
			'showcity'        => true,
			'showbookbtn'     => true,
			'showreadmorebtn' => true,
			'city'            => null,
			'courselevel'     => null,
			'searchCourse'    => null,
			'filtercity'      => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-listview'
	);
	$str               = include EDUADMIN_PLUGIN_PATH . '/content/template/listTemplate/' . $attributes['template'] . '.php';
	EDU()->stop_timer( $t );

	return $str;
}

function eduadmin_get_object_interest( $attributes ) {
	$t          = EDU()->start_timer( __METHOD__ );
	$attributes = shortcode_atts(
		array(
			'courseid' => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-objectinterest'
	);
	$str        = include EDUADMIN_PLUGIN_PATH . '/content/template/interestRegTemplate/interest-reg-object.php';
	EDU()->stop_timer( $t );

	return $str;
}

function eduadmin_get_event_interest( $attributes ) {
	$t          = EDU()->start_timer( __METHOD__ );
	$attributes = shortcode_atts(
		array(),
		normalize_empty_atts( $attributes ),
		'eduadmin-eventinterest'
	);
	$str        = include EDUADMIN_PLUGIN_PATH . '/content/template/interestRegTemplate/interest-reg-event.php';
	EDU()->stop_timer( $t );

	return $str;
}

function eduadmin_get_detail_view( $attributes ) {
	$t                           = EDU()->start_timer( __METHOD__ );
	$selected_template           = get_option( 'eduadmin-detailTemplate', 'template_A' );
	$attributes                  = shortcode_atts(
		array(
			'template'       => $selected_template,
			'courseid'       => null,
			'customtemplate' => null,
			'showmore'       => null,
			'hide'           => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-detailview'
	);
	EDU()->session['checkEmail'] = null;
	EDU()->session['needsLogin'] = null;
	unset( EDU()->session['checkEmail'] );
	unset( EDU()->session['needsLogin'] );
	if ( isset( EDU()->session['eduadmin-loginUser']->NewCustomer ) ) {
		unset( EDU()->session['eduadmin-loginUser']->NewCustomer );
	}

	EDU()->session->regenerate_id( true );
	if ( empty( $attributes['customtemplate'] ) || 1 !== intval( $attributes['customtemplate'] ) ) {
		$str = include_once EDUADMIN_PLUGIN_PATH . '/content/template/detailTemplate/' . $attributes['template'] . '.php';
		EDU()->stop_timer( $t );

		return $str;
	}

	EDU()->stop_timer( $t );

	return '';
}

function eduadmin_get_course_public_pricename( $attributes ) {
	$t = EDU()->start_timer( __METHOD__ );
	global $wp_query;
	$attributes = shortcode_atts(
		array(
			'courseid'       => null,
			'orderby'        => null,
			'order'          => null,
			'numberofprices' => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin_coursepublicpricename'
	);

	if ( empty( $attributes['courseid'] ) || $attributes['courseid'] <= 0 ) {
		if ( isset( $wp_query->query_vars['courseId'] ) ) {
			$course_id = $wp_query->query_vars['courseId'];
		} else {
			EDU()->stop_timer( $t );

			return 'Missing courseId in attributes';
		}
	} else {
		$course_id = $attributes['courseid'];
	}
	EDU()->stop_timer( $t );

	return include_once EDUADMIN_PLUGIN_PATH . '/content/template/myPagesTemplate/course-price-names.php';
}

function edu_no_index() {
	$t = EDU()->start_timer( __METHOD__ );
	global $wp_query;
	$detailpage = get_option( 'eduadmin-detailViewPage' );
	if ( isset( $wp_query->queried_object ) ) {
		if ( false !== $detailpage && $detailpage === $wp_query->queried_object->ID && ! isset( $wp_query->query['courseId'] ) ) {
			echo '<meta name="robots" content="noindex" />';
		}
	}
	EDU()->stop_timer( $t );
}

add_action( 'wp_head', 'edu_no_index' );

function eduadmin_get_booking_view( $attributes ) {
	$t = EDU()->start_timer( __METHOD__ );
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	$selected_template = get_option( 'eduadmin-bookingTemplate', 'template_A' );
	$attributes        = shortcode_atts(
		array(
			'template'               => $selected_template,
			'courseid'               => null,
			'hideinvoiceemailfield'  => null,
			'showinvoiceinformation' => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-bookingview'
	);
	if ( empty( get_option( 'eduadmin-useLogin', false ) ) || ( isset( EDU()->session['eduadmin-loginUser'] ) && ( ( isset( EDU()->session['eduadmin-loginUser']->Contact->PersonId ) && 0 !== EDU()->session['eduadmin-loginUser']->Contact->PersonId ) || isset( EDU()->session['eduadmin-loginUser']->NewCustomer ) ) ) ) {
		$str = include_once EDUADMIN_PLUGIN_PATH . '/content/template/bookingTemplate/' . $attributes['template'] . '.php';
	} else {
		$str = include_once EDUADMIN_PLUGIN_PATH . '/content/template/bookingTemplate/login-view.php';
	}
	EDU()->stop_timer( $t );

	return $str;
}

function eduadmin_get_detailinfo( $attributes ) {
	$t = EDU()->start_timer( __METHOD__ );
	global $wp_query;
	$attributes = shortcode_atts(
		array(
			'courseid'                  => null,
			'coursename'                => null,
			'coursepublicname'          => null,
			'courselevel'               => null,
			'courseimage'               => null,
			'courseimagetext'           => null,
			'coursedays'                => null,
			'coursestarttime'           => null,
			'courseendtime'             => null,
			'courseprice'               => null,
			'eventprice'                => null,
			'coursedescriptionshort'    => null,
			'coursedescription'         => null,
			'coursegoal'                => null,
			'coursetarget'              => null,
			'courseprerequisites'       => null,
			'courseafter'               => null,
			'coursequote'               => null,
			'courseeventlist'           => null,
			'showmore'                  => null,
			'courseattributeid'         => null,
			'courseeventlistfiltercity' => null,
			'pagetitlejs'               => null,
			'bookurl'                   => null,
			'courseinquiryurl'          => null,
			'order'                     => null,
			'orderby'                   => null,
			//'coursesubject' => null
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-detailinfo'
	);

	$ret_str = '';

	if ( empty( $attributes['courseid'] ) || str_replace( array(
		                                                      '&#8221;',
		                                                      '&#8243;',
	                                                      ), '', $attributes['courseid'] ) <= 0 ) {
		if ( isset( $wp_query->query_vars['courseId'] ) ) {
			$course_id = $wp_query->query_vars['courseId'];
		} else {
			EDU()->stop_timer( $t );

			return 'Missing courseid in attributes';
		}
	} else {
		$course_id = str_replace(
			array(
				'&#8221;',
				'&#8243;',
			),
			'',
			$attributes['courseid']
		);
	}

	$api_key = get_option( 'eduadmin-api-key' );

	if ( ! $api_key || empty( $api_key ) ) {
		EDU()->stop_timer( $t );

		return 'Please complete the configuration: <a href="' . admin_url() . 'admin.php?page=eduadmin-settings">EduAdmin - Api Authentication</a>';
	} else {
		$edo          = get_transient( 'eduadmin-object_' . $course_id . '_json'. '__' . EDU()->version );
		$fetch_months = get_option( 'eduadmin-monthsToFetch', 6 );
		if ( ! is_numeric( $fetch_months ) ) {
			$fetch_months = 6;
		}
		if ( ! $edo ) {
			$expands = array();

			$expands['Subjects']   = '';
			$expands['Categories'] = '';
			$expands['PriceNames'] = '$filter=PublicPriceName;';
			$expands['Events']     =
				'$filter=' .
				'HasPublicPriceName' .
				' and StatusId eq 1' .
				' and CustomerId eq null' .
				' and LastApplicationDate ge ' . date( 'c' ) .
				' and StartDate le ' . date( 'c', strtotime( 'now 23:59:59 +' . $fetch_months . ' months' ) ) .
				' and EndDate ge ' . date( 'c', strtotime( 'now' ) ) .
				';' .
				'$expand=PriceNames($filter=PublicPriceName),EventDates($orderby=StartDate),Sessions($expand=PriceNames($filter=PublicPriceName;)),PaymentMethods' .
				';' .
				'$orderby=StartDate asc' .
				';';

			$expands['CustomFields'] = '$filter=ShowOnWeb';

			$expand_arr = array();
			foreach ( $expands as $key => $value ) {
				if ( empty( $value ) ) {
					$expand_arr[] = $key;
				} else {
					$expand_arr[] = $key . '(' . $value . ')';
				}
			}

			$edo = wp_json_encode( EDUAPI()->OData->CourseTemplates->GetItem(
				$course_id,
				null,
				join( ',', $expand_arr )
			) );
			set_transient( 'eduadmin-object_' . $course_id . '_json'. '__' . EDU()->version, $edo, 10 );
		}

		$selected_course = false;

		if ( ! empty( $edo ) ) {
			$selected_course = json_decode( $edo, true );
		}

		if ( ! is_array( $selected_course ) ) {
			EDU()->stop_timer( $t );
			EDU()->write_debug( $attributes );

			return 'Course with ID ' . $course_id . ' could not be found.';
		} else {
			$getorg_json = get_transient( 'eduadmin-organisation_json'. '__' . EDU()->version );
			if ( ! $getorg_json ) {
				$org = EDUAPI()->REST->Organisation->GetOrganisation();
				set_transient( 'eduadmin-organisation_json'. '__' . EDU()->version, wp_json_encode( $org ), 10 );
			} else {
				$org = json_decode( $getorg_json, true );
			}
			$inc_vat = $org['PriceIncVat'];

			if ( isset( $attributes['coursename'] ) ) {
				$ret_str .= $selected_course['InternalCourseName'];
			}
			if ( isset( $attributes['coursepublicname'] ) ) {
				$ret_str .= $selected_course['CourseName'];
			}
			if ( isset( $attributes['courseimage'] ) ) {
				$ret_str .= $selected_course['ImageUrl'];
			}
			if ( isset( $attributes['coursedays'] ) ) {
				/* translators: 1: Number of days */
				$ret_str .= sprintf( _n( '%1$d day', '%1$d days', $selected_course['Days'], 'eduadmin-booking' ), $selected_course['Days'] );
			}
			if ( isset( $attributes['coursestarttime'] ) ) {
				$ret_str .= $selected_course['StartTime'];
			}
			if ( isset( $attributes['courseendtime'] ) ) {
				$ret_str .= $selected_course['EndTime'];
			}
			if ( isset( $attributes['coursedescriptionshort'] ) ) {
				$ret_str .= $selected_course['CourseDescriptionShort'];
			}
			if ( isset( $attributes['coursedescription'] ) ) {
				$ret_str .= $selected_course['CourseDescription'];
			}
			if ( isset( $attributes['coursegoal'] ) ) {
				$ret_str .= $selected_course['CourseGoal'];
			}
			if ( isset( $attributes['coursetarget'] ) ) {
				$ret_str .= $selected_course['TargetGroup'];
			}
			if ( isset( $attributes['courseprerequisites'] ) ) {
				$ret_str .= $selected_course['Prerequisites'];
			}
			if ( isset( $attributes['courseafter'] ) ) {
				$ret_str .= $selected_course['CourseAfter'];
			}
			if ( isset( $attributes['coursequote'] ) ) {
				$ret_str .= $selected_course['Quote'];
			}
			if ( isset( $attributes['coursesubject'] ) ) {
				$subject_names = array();
				foreach ( $selected_course['Subjects'] as $subj ) {
					$subject_names[] = $subj['SubjectName'];
				}
				$ret_str .= join( ', ', $subject_names );
			}
			if ( isset( $attributes['courselevel'] ) ) {
				if ( ! empty( $selected_course['CourseLevelId'] ) ) {
					$course_level = EDUAPI()->OData->CourseLevels->GetItem( $selected_course['CourseLevelId'] );
					if ( ! empty( $course_level ) ) {
						$ret_str .= $course_level['Name'];
					}
				}
			}
			if ( isset( $attributes['courseattributeid'] ) ) {
				$attrid = $attributes['courseattributeid'];

				foreach ( $selected_course['CustomFields'] as $cf ) {
					if ( $cf['CustomFieldId'] === $attrid ) {
						switch ( $cf['CustomFieldType'] ) {
							case 'Text':
							case 'Html':
							case 'Textarea':
								$ret_str .= wp_kses_post( $cf['CustomFieldValue'] );
								break;
							case 'Dropdown':
								$ret_str .= wp_kses_post( $cf['CustomFieldAlternativeValue'] );
								break;
						}
						break;
					}
				}
			}

			if ( isset( $attributes['courseprice'] ) ) {
				$prices = array();

				foreach ( $selected_course['PriceNames'] as $pn ) {
					$prices[ $pn['PriceNameId'] ] = $pn;
				}

				$currency = get_option( 'eduadmin-currency', 'SEK' );
				if ( 1 === count( $prices ) ) {
					$ret_str .= esc_html( convert_to_money( current( $prices )['Price'], $currency ) . ' ' . ( $inc_vat ? __( 'inc vat', 'eduadmin-booking' ) : __( 'ex vat', 'eduadmin-booking' ) ) ) . "\n";
				} else {
					foreach ( $prices as $price ) {
						$ret_str .= esc_html( sprintf( '%1$s: %2$s', $price['PriceNameDescription'], convert_to_money( $price['Price'], $currency ) ) . ' ' . ( $inc_vat ? __( 'inc vat', 'eduadmin-booking' ) : __( 'ex vat', 'eduadmin-booking' ) ) ) . "<br />\n";
					}
				}
			}

			if ( isset( $attributes['eventprice'] ) ) {
				$events = $selected_course['Events'];
				$prices = array();

				foreach ( $events as $e ) {
					foreach ( $e['PriceNames'] as $pn ) {
						$prices[ $pn['PriceNameId'] ] = $pn;
					}
				}

				$currency = get_option( 'eduadmin-currency', 'SEK' );
				if ( 1 === count( $prices ) ) {
					$ret_str .= esc_html( convert_to_money( current( $prices )['Price'], $currency ) . ' ' . ( $inc_vat ? __( 'inc vat', 'eduadmin-booking' ) : __( 'ex vat', 'eduadmin-booking' ) ) ) . "\n";
				} else {
					foreach ( $prices as $price ) {
						$ret_str .= esc_html( sprintf( '%1$s: %2$s', $price['PriceNameDescription'], convert_to_money( $price['Price'], $currency ) ) . ' ' . ( $inc_vat ? __( 'inc vat', 'eduadmin-booking' ) : __( 'ex vat', 'eduadmin-booking' ) ) ) . "<br />\n";
					}
				}
			}

			if ( isset( $attributes['pagetitlejs'] ) ) {
				$new_title = $selected_course['CourseName'];

				$ret_str .= '
				<script type="text/javascript">
				(function() {
					var title = document.title;
					document.title = \'' . esc_js( $new_title ) . ' | \' + title;
				})();
				</script>';
			}

			if ( isset( $attributes['bookurl'] ) ) {
				$surl     = get_home_url();
				$cat      = get_option( 'eduadmin-rewriteBaseUrl' );
				$base_url = $surl . '/' . $cat;

				$name = ( ! empty( $selected_course['CourseName'] ) ? $selected_course['CourseName'] : $selected_course['InternalCourseName'] );

				$ret_str .= esc_url( $base_url . '/' . make_slugs( $name ) . '__' . $selected_course['CourseTemplateId'] . '/book/' . edu_get_query_string() . '&_=' . time() );
			}

			if ( isset( $attributes['courseinquiryurl'] ) ) {
				$surl     = get_home_url();
				$cat      = get_option( 'eduadmin-rewriteBaseUrl' );
				$base_url = $surl . '/' . $cat;

				$name = ( ! empty( $selected_course['CourseName'] ) ? $selected_course['CourseName'] : $selected_course['InternalCourseName'] );

				$ret_str .= esc_url( $base_url . '/' . make_slugs( $name ) . '__' . $selected_course['CourseTemplateId'] . '/interest/' . edu_get_query_string() . '&_=' . time() );
			}

			if ( isset( $attributes['courseeventlist'] ) ) {
				$events = $selected_course['Events'];

				if ( ! empty( $attributes['courseeventlistfiltercity'] ) ) {
					$_city  = $attributes['courseeventlistfiltercity'];
					$events = array_filter( $events, function( $_event ) use ( $_city ) {
						return $_event['City'] === $_city;
					} );
				}

				$group_by_city       = get_option( 'eduadmin-groupEventsByCity', false );
				$group_by_city_class = '';
				if ( $group_by_city ) {
					$group_by_city_class = ' noCity';
				}

				$custom_order_by       = null;
				$custom_order_by_order = null;
				if ( ! empty( $attributes['orderby'] ) ) {
					$custom_order_by = $attributes['orderby'];
				}

				if ( ! empty( $attributes['order'] ) ) {
					$custom_order_by_order = $attributes['order'];
				}

				if ( null !== $custom_order_by ) {
					$orderby   = explode( ' ', $custom_order_by );
					$sortorder = explode( ' ', $custom_order_by_order );
					foreach ( $orderby as $od => $v ) {
						if ( isset( $sortorder[ $od ] ) ) {
							$or = $sortorder[ $od ];
						} else {
							$or = 'ASC';
						}
					}
				}

				$surl = get_home_url();
				$cat  = get_option( 'eduadmin-rewriteBaseUrl' );

				$last_city = '';

				$show_more        = isset( $attributes['showmore'] ) && ! empty( $attributes['showmore'] ) ? $attributes['showmore'] : -1;
				$spot_left_option = get_option( 'eduadmin-spotsLeft', 'exactNumbers' );
				$always_few_spots = get_option( 'eduadmin-alwaysFewSpots', '3' );
				$spot_settings    = get_option( 'eduadmin-spotsSettings', "1-5\n5-10\n10+" );

				$base_url = $surl . '/' . $cat;
				$name     = ( ! empty( $selected_course['CourseName'] ) ? $selected_course['CourseName'] : $selected_course['InternalCourseName'] );

				$object_interest_page      = get_option( 'eduadmin-interestObjectPage' );
				$allow_interest_reg_object = get_option( 'eduadmin-allowInterestRegObject', false );

				$event_interest_page      = get_option( 'eduadmin-interestEventPage' );
				$allow_interest_reg_event = get_option( 'eduadmin-allowInterestRegEvent', false );

				$ret_str .= '<div class="eduadmin">';
				$ret_str .= '<div class="event-table eventDays" data-eduwidget="eventlist" ';
				$ret_str .= 'data-objectid="' . esc_attr( $selected_course['CourseTemplateId'] );
				$ret_str .= '" data-spotsleft="' . esc_attr( $spot_left_option );
				$ret_str .= '" data-showmore="' . esc_attr( $show_more );
				$ret_str .= '" data-groupbycity="' . esc_attr( $group_by_city ) . '"';
				$ret_str .= '" data-spotsettings="' . esc_attr( $spot_settings ) . '"';
				$ret_str .= '" data-fewspots="' . esc_attr( $always_few_spots ) . '"';
				$ret_str .= ( ! empty( $attributes['courseeventlistfiltercity'] ) ? ' data-city="' . esc_attr( $attributes['courseeventlistfiltercity'] ) . '"' : '' );
				$ret_str .= ' data-fetchmonths="' . esc_attr( $fetch_months ) . '"';
				$ret_str .= ( isset( $_REQUEST['eid'] ) ? ' data-event="' . intval( $_REQUEST['eid'] ) . '"' : '' );
				$ret_str .= ' data-order="' . esc_attr( $custom_order_by ) . '"';
				$ret_str .= ' data-orderby="' . esc_attr( $custom_order_by_order ) . '"';
				$ret_str .= ' data-showvenue="' . esc_attr( get_option( 'eduadmin-showEventVenueName', false ) ) . '"';
				$ret_str .= ' data-eventinquiry="' . esc_attr( get_option( 'eduadmin-allowInterestRegEvent', false ) ) . '"';
				$ret_str .= '>';

				$i                = 0;
				$has_hidden_dates = false;
				$show_event_venue = get_option( 'eduadmin-showEventVenueName', false );

				$event_interest_page = get_option( 'eduadmin-interestEventPage' );

				foreach ( $events as $ev ) {
					$spots_left = $ev['ParticipantNumberLeft'];

					if ( ! empty( $_REQUEST['eid'] ) ) {
						if ( $ev['EventId'] !== intval( $_REQUEST['eid'] ) ) {
							continue;
						}
					}

					ob_start();
					include EDUADMIN_PLUGIN_PATH . '/content/template/detailTemplate/blocks/event-item.php';
					$ret_str .= ob_get_clean();

					$last_city = $ev['City'];
					$i++;
				}
				if ( empty( $events ) ) {
					$ret_str .= '<div class="noDatesAvailable"><i>' . esc_html__( 'No available dates for the selected course', 'eduadmin-booking' ) . '</i></div>';
				}
				if ( $has_hidden_dates ) {
					$ret_str .= '<div class="eventShowMore"><a class="neutral-btn" href="javascript://" onclick="eduDetailView.ShowAllEvents(\'eduev' . esc_attr( ( $group_by_city ? '-' . $last_city : '' ) ) . '\', this);">' . esc_html__( 'Show all events', 'eduadmin-booking' ) . '</a></div>';
				}
				$ret_str .= '</div></div>';
			}
		}
	}
	EDU()->stop_timer( $t );

	return $ret_str;
}

function eduadmin_get_login_widget( $attributes ) {
	$t          = EDU()->start_timer( __METHOD__ );
	$attributes = shortcode_atts(
		array(
			'logintext'  => __( 'Log in', 'eduadmin-booking' ),
			'logouttext' => __( 'Log out', 'eduadmin-booking' ),
			'guesttext'  => __( 'Guest', 'eduadmin-booking' ),
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-loginwidget'
	);

	$surl = get_home_url();
	$cat  = get_option( 'eduadmin-rewriteBaseUrl' );

	$base_url = $surl . '/' . $cat;
	if ( isset( EDU()->session['eduadmin-loginUser'] ) ) {
		$user = EDU()->session['eduadmin-loginUser'];
	}
	EDU()->stop_timer( $t );

	return '<div class="eduadminLogin" data-eduwidget="loginwidget"
	data-logintext="' . esc_attr( $attributes['logintext'] ) . '"
	data-logouttext="' . esc_attr( $attributes['logouttext'] ) . '"
	data-guesttext="' . esc_attr( $attributes['guesttext'] ) . '">' .
	       '</div>';
}

function eduadmin_get_login_view( $attributes ) {
	$t = EDU()->start_timer( __METHOD__ );
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	$attributes = shortcode_atts(
		array(
			'logintext'  => __( 'Log in', 'eduadmin-booking' ),
			'logouttext' => __( 'Log out', 'eduadmin-booking' ),
			'guesttext'  => __( 'Guest', 'eduadmin-booking' ),
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-loginview'
	);
	EDU()->stop_timer( $t );

	return include_once EDUADMIN_PLUGIN_PATH . '/content/template/myPagesTemplate/login.php';
}

function eduadmin_get_programme_list( $attributes ) {
	$attributes = shortcode_atts(
		array(),
		normalize_empty_atts( $attributes ),
		'eduadmin-programmelist'
	);

	$programmes = EDUAPI()->OData->Programmes->Search(
		null,
		'ShowOnWeb',
		'ProgrammeStarts(' .
		'$filter=' .
		'HasPublicPriceName' .
		' and (ApplicationOpenDate le ' . date( 'c' ) . ' or ApplicationOpenDate eq null)' .
		' and StartDate ge ' . date( 'c' ) .
		';' .
		'$orderby=' .
		'StartDate),Courses'
	);

	include_once EDUADMIN_PLUGIN_PATH . '/content/template/programme/list.php';
}

function eduadmin_get_programme_details( $attributes ) {
	$attributes = shortcode_atts(
		array(
			'programmeid' => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-programmedetail'
	);

	global $wp_query;

	if ( ! empty( $wp_query->query_vars['edu_programme'] ) ) {
		$exploded_id  = explode( '_', $wp_query->query_vars['edu_programme'] )[1];
		$programme_id = $exploded_id;
	} elseif ( ! empty( $attributes['programmeid'] ) ) {
		$programme_id = $attributes['programmeid'];
	} else {
		$programme_id = null;
	}

	if ( ! empty( $programme_id ) ) {
		$programme = EDUAPI()->OData->Programmes->GetItem(
			$programme_id,
			null,
			'ProgrammeStarts(' .
			'$filter=' .
			'HasPublicPriceName' .
			' and (ApplicationOpenDate le ' . date( 'c' ) . ' or ApplicationOpenDate eq null)' .
			' and StartDate ge ' . date( 'c' ) .
			';' .
			'$orderby=' .
			'StartDate' .
			';' .
			'$expand=' .
			'Courses,Events' .
			'),Courses,PriceNames'
		);

		include_once EDUADMIN_PLUGIN_PATH . '/content/template/programme/detail.php';
	}
}

function eduadmin_get_programme_booking( $attributes ) {
	$attributes = shortcode_atts(
		array(
			'programmeid'      => null,
			'programmestartid' => null,
		),
		normalize_empty_atts( $attributes ),
		'eduadmin-programmebooking'
	);

	global $wp_query;

	if ( ! empty( $wp_query->query_vars['edu_programme'] ) ) {
		$exploded_id  = explode( '_', $wp_query->query_vars['edu_programme'] )[1];
		$programme_id = $exploded_id;
	} elseif ( ! empty( $attributes['programmeid'] ) ) {
		$programme_id = $attributes['programmeid'];
	} else {
		$programme_id = null;
	}

	$programmestart_id = $_GET['id'];

	if ( ! empty( $programmestart_id ) ) {
		$programme = EDUAPI()->OData->ProgrammeStarts->GetItem(
			$programmestart_id,
			null,
			'Courses,Events,PaymentMethods,PriceNames'
		);

		include_once EDUADMIN_PLUGIN_PATH . '/content/template/programme/book.php';
	}
}

if ( is_callable( 'add_shortcode' ) ) {
	add_shortcode( 'eduadmin-listview', 'eduadmin_get_list_view' );
	add_shortcode( 'eduadmin-detailview', 'eduadmin_get_detail_view' );
	add_shortcode( 'eduadmin-bookingview', 'eduadmin_get_booking_view' );
	add_shortcode( 'eduadmin-detailinfo', 'eduadmin_get_detailinfo' );
	add_shortcode( 'eduadmin-loginwidget', 'eduadmin_get_login_widget' );
	add_shortcode( 'eduadmin-loginview', 'eduadmin_get_login_view' );
	add_shortcode( 'eduadmin-objectinterest', 'eduadmin_get_object_interest' );
	add_shortcode( 'eduadmin-eventinterest', 'eduadmin_get_event_interest' );
	add_shortcode( 'eduadmin-coursepublicpricename', 'eduadmin_get_course_public_pricename' );

	add_shortcode( 'eduadmin-programme-list', 'eduadmin_get_programme_list' );
	add_shortcode( 'eduadmin-programme-detail', 'eduadmin_get_programme_details' );
	add_shortcode( 'eduadmin-programme-book', 'eduadmin_get_programme_booking' );
}
