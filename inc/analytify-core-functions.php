<?php
/**
 * Core Functions for WP Analytify
 *
 * This file now includes all the split core function files for better organization.
 * All functions are kept as standalone functions for simplicity and backward compatibility.
 *
 * @package WP_Analytify
 * @since 8.0.0
 */

// Include all split core function files.
require_once __DIR__ . '/core-traits/dates.php';
require_once __DIR__ . '/core-traits/utilities.php';
require_once __DIR__ . '/core-traits/admin-footer.php';
require_once __DIR__ . '/core-traits/dashboard.php';
require_once __DIR__ . '/core-traits/profile-helpers.php';
require_once __DIR__ . '/core-traits/functions-class.php';

// The main file is now much cleaner and organized!
// All functionality is preserved in the included files.
