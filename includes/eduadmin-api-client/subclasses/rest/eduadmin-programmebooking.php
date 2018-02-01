<?php

	/**
	 * Class EduAdmin_REST_ProgrammeBooking
	 */
	class EduAdmin_REST_ProgrammeBooking extends EduAdminRESTClient {
		protected $api_url = "/v1/ProgrammeBooking";

		/**
		 * @param EduAdmin_Data_ProgrammeBooking $programmeBooking
		 *
		 * @return mixed
		 */
		public function Book( EduAdmin_Data_ProgrammeBooking $programmeBooking ) {
			return parent::POST(
				"",
				$programmeBooking,
				get_called_class() . "|" . __FUNCTION__
			);
		}

		/**
		 * @param integer            $programmeBookingId
		 * @param EduAdmin_Data_Mail $pbEmail
		 *
		 * @return mixed
		 */
		public function SendEmail( $programmeBookingId, EduAdmin_Data_Mail $pbEmail ) {
			return parent::POST(
				"/$programmeBookingId/Email/Send",
				$pbEmail,
				get_called_class() . "|" . __FUNCTION__
			);
		}

		/**
		 * @param integer                    $programmeBookingId
		 * @param EduAdmin_Data_MailAdvanced $pbEmail
		 *
		 * @return mixed
		 */
		public function SendEmailAdvanced( $programmeBookingId, EduAdmin_Data_MailAdvanced $pbEmail ) {
			return parent::POST(
				"/$programmeBookingId/Email/SendAdvanced",
				$pbEmail,
				get_called_class() . "|" . __FUNCTION__
			);
		}

		/**
		 * @param integer                              $programmeBookingId
		 * @param EduAdmin_Data_ProgrammeBooking_Patch $patch
		 *
		 * @return mixed
		 */
		public function PatchBooking( $programmeBookingId, EduAdmin_Data_ProgrammeBooking_Patch $patch ) {
			return parent::PATCH(
				"/$programmeBookingId",
				$patch,
				get_called_class() . "|" . __FUNCTION__
			);
		}
	}