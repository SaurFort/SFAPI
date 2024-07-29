<?php
    /** This file is made to test some functionnality before the implementation in the complete API */
    
    function generateUuidV4WithPrefix($prefix) {
        // Génère 16 octets aléatoires
        $bytes = random_bytes(16);
    
        // Modifie les octets pour respecter le format UUID v4
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40); // version 4
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80); // variante
    
        // Formate les octets en UUID avec les tirets
        $uuid = sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            bin2hex(substr($bytes, 6, 2)),
            bin2hex(substr($bytes, 8, 2)),
            bin2hex(substr($bytes, 10))
        );
    
        // Ajoute le préfixe
        return $prefix . $uuid;
    }
    
    // Utilisation
    echo generateUuidV4WithPrefix("apiv0_");
?>