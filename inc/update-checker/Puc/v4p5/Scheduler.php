<?php
if ( !class_exists('Puc_v4p5_Scheduler', false) ):

	/**
	 * The scheduler decides when and how often to check for updates.
	 * It calls @see Puc_v4p5_UpdateChecker::checkForUpdates() to perform the actual checks.
	 */
	class Puc_v4p5_Scheduler {
		public $checkPeriod = 12; //How often to check for updates (in hours).
		public $throttleRedundantChecks = false; //Check less often if we already know that an update is available.
		public $throttledCheckPeriod = 72;

		protected $hourlyCheckHooks = array('load-update.php');

		/**
		 * @var Puc_v4p5_UpdateChecker
		 */
		protected $updateChecker;

		private $cronHook = null;

		/**
		 * Scheduler constructor.
		 *
		 * @param Puc_v4p5_UpdateChecker $updateChecker
		 * @param int $checkPeriod How often to check for updates (in hours).
		 * @param array $hourlyHooks
		 */
		public function __construct($updateChecker, $checkPeriod, $hourlyHooks = array('load-plugins.php')) {
			$this->updateChecker = $updateChecker;
			$this->checkPeriod = $checkPeriod;

			//Set up the periodic update checks
			$this->cronHook = $this->updateChecker->getUniqueName('cron_check_updates');
			if ( $this->checkPeriod > 0 ){

				//Trigger the check via Cron.
				//Try to use one of the default schedules if possible as it's less likely to conflict
				//with other plugins and their custom schedules.
				$defaultSchedules = array(
					1  => 'hourly',
					12 => 'twicedaily',
					24 => 'daily',
				);
				if ( array_key_exists($this->checkPeriod, $defaultSchedules) ) {
					$scheduleName = $defaultSchedules[$this->checkPeriod];
				} else {
					//Use a custom cron schedule.
					$scheduleName = 'every' . $this->checkPeriod . 'hours';
					add_filter('cron_schedules', array($this, '_addCustomSchedule'));
				}

				if ( !wp_next_scheduled($this->cronHook) && !defined('WP_INSTALLING') ) {
					wp_schedule_event(time(), $scheduleName, $this->cronHook);
				}
				add_action($this->cronHook, array($this, 'maybeCheckForUpdates'));

				//In case Cron is disabled or unreliable, we also manually trigger
				//the periodic checks while the user is browsing the Dashboard.
				add_action( 'admin_init', array($this, 'maybeCheckForUpdates') );

				//Like WordPress itself, we check more often on certain pages.
				/** @see wp_update_plugins */
				add_action('load-update-core.php', array($this, 'maybeCheckForUpdates'));
				//"load-update.php" and "load-plugins.php" or "load-themes.php".
				$this->hourlyCheckHooks = array_merge($this->hourlyCheckHooks, $hourlyHooks);
				foreach($this->hourlyCheckHooks as $hook) {
					add_action($hook, array($this, 'maybeCheckForUpdates'));
				}
				//This hook fires after a bulk update is complete.
				add_action('upgrader_process_complete', array($this, 'maybeCheckForUpdates'), 11, 0);

			} else {
				//Periodic checks are disabled.
				wp_clear_scheduled_hook($this->cronHook);
			}
		}

		/**
		 * Check for updates if the configured check interval has already elapsed.
		 * Will use a shorter check interval on certain admin pages like "Dashboard -> Updates" or when doing cron.
		 *
		 * You can override the default behaviour by using the "puc_check_now-$slug" filter.
		 * The filter callback will be passed three parameters:
		 *     - Current decision. TRUE = check updates now, FALSE = don't check now.
		 *     - Last check time as a Unix timestamp.
		 *     - Configured check period in hours.
		 * Return TRUE to check for updates immediately, or FALSE to cancel.
		 *
		 * This method is declared public because it's a hook callback. Calling it directly is not recommended.
		 */
		public function maybeCheckForUpdates(){
			if ( empty($this->checkPeriod) ){
				return;
			}

			$state = $this->updateChecker->getUpdateState();
			$shouldCheck = ($state->timeSinceLastCheck() >= $this->getEffectiveCheckPeriod());

			//Let plugin authors substitute their own algorithm.
			$shouldCheck = apply_filters(
				$this->updateChecker->getUniqueName('check_now'),
				$shouldCheck,
				$state->getLastCheck(),
				$this->checkPeriod
			);

			if ( $shouldCheck ) {
				$this->updateChecker->checkForUpdates();
			}
		}

		/**
		 * Calculate the actual check period based on the current status and environment.
		 *
		 * @return int Check period in seconds.
		 */
		protected function getEffectiveCheckPeriod() {
			$currentFilter = current_filter();
			if ( in_array($currentFilter, array('load-update-core.php', 'upgrader_process_complete')) ) {
				//Check more often when the user visits "Dashboard -> Updates" or does a bulk update.
				$period = 60;
			} else if ( in_array($currentFilter, $this->hourlyCheckHooks) ) {
				//Also check more often on /wp-admin/update.php and the "Plugins" or "Themes" page.
				$period = 3600;
			} else if ( $this->throttleRedundantChecks && ($this->updateChecker->getUpdate() !== null) ) {
				//Check less frequently if it's already known that an update is available.
				$period = $this->throttledCheckPeriod * 3600;
			} else if ( defined('DOING_CRON') && constant('DOING_CRON') ) {
				//WordPress cron schedules are not exact, so lets do an update check even
				//if slightly less than $checkPeriod hours have elapsed since the last check.
				$cronFuzziness = 20 * 60;
				$period = $this->checkPeriod * 3600 - $cronFuzziness;
			} else {
				$period = $this->checkPeriod * 3600;
			}

			return $period;
		}

		/**
		 * Add our custom schedule to the array of Cron schedules used by WP.
		 *
		 * @param array $schedules
		 * @return array
		 */
		public function _addCustomSchedule($schedules){
			if ( $this->checkPeriod && ($this->checkPeriod > 0) ){
				$scheduleName = 'every' . $this->checkPeriod . 'hours';
				$schedules[$scheduleName] = array(
					'interval' => $this->checkPeriod * 3600,
					'display' => sprintf('Every %d hours', $this->checkPeriod),
				);
			}
			return $schedules;
		}

		/**
		 * Remove the scheduled cron event that the library uses to check for updates.
		 *
		 * @return void
		 */
		public function removeUpdaterCron(){
			wp_clear_scheduled_hook($this->cronHook);
		}

		/**
		 * Get the name of the update checker's WP-cron hook. Mostly useful for debugging.
		 *
		 * @return string
		 */
		public function getCronHookName() {
			return $this->cronHook;
		}
	}

endif;
