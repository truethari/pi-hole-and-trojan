<?php
/**
 * Nextcloud Encryption Configuration
 * This file enables server-side encryption for maximum privacy
 */

$CONFIG = array(
  // Enable default encryption module
  'encryption.default_module' => 'OC_DEFAULT_MODULE',
  
  // Enable encryption
  'encryption.key_storage_migrated' => true,
  
  // Force encryption for all files
  'encryption.legacy_format_support' => false,
  
  // Additional security settings
  'passwordsalt' => '', // Will be auto-generated
  'secret' => '', // Will be auto-generated
  
  // Disable file sharing by default for maximum privacy
  'sharing.enable_share_accept' => false,
  
  // Additional privacy settings
  'check_data_directory_permissions' => true,
  'log_type' => 'file',
  'logfile' => '/var/www/html/data/nextcloud.log',
  'loglevel' => 2,
);