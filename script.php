<?php
    /** This file is made to test some functionnality before the implementation in the complete API */
    
    function generateUuidV4WithPrefix($prefix) {
        $bytes = random_bytes(16);
    
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    
        $uuid = sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            bin2hex(substr($bytes, 6, 2)),
            bin2hex(substr($bytes, 8, 2)),
            bin2hex(substr($bytes, 10))
        );
    
        return $prefix . $uuid;
    }
    
    //echo generateUuidV4WithPrefix("apiv0_");
?>