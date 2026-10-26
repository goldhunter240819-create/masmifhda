<?php
namespace App\Core;

class Branding {
    private static $cache = null;
    private static $instCache = null;

    public static function get() {
        if (self::$cache !== null) return self::$cache;
        
        $db = Database::connect('core');
        $portal = $db->query("SELECT * FROM app_modules WHERE app_code = 'portal'")->fetch();

        // Ambil logo institusi sebagai sumber utama identitas visual
        $instLogo    = self::getInstitusiLogo();
        $instFavicon = self::getInstitusiFavicon();

        self::$cache = [
            'name'    => !empty($portal['app_name']) ? $portal['app_name'] : 'GH-SSS Portal',
            'logo'    => $instLogo,
            'favicon' => $instFavicon,
            'theme'   => !empty($portal['theme_color']) ? "#" . $portal['theme_color'] : "#0ea5e9",
            'title'   => !empty($portal['tab_title']) ? $portal['tab_title'] : 'GH-SSS Portal - MAS MIFHDA',
            // Logo & favicon langsung dari institusi (dipakai sidebar/favicon SIAKAD)
            'inst_logo'    => $instLogo,
            'inst_favicon' => $instFavicon,
        ];
        
        return self::$cache;
    }

    /**
     * Kembalikan URL logo institusi dari tabel `institusi`.
     * Fallback ke logo default jika belum diupload.
     */
    public static function getInstitusiLogo() {
        if (self::$instCache !== null) return self::$instCache['logo'];
        self::_loadInstitusi();
        return self::$instCache['logo'];
    }

    /**
     * Kembalikan URL favicon institusi (sama dengan logo jika tidak ada favicon terpisah).
     */
    public static function getInstitusiFavicon() {
        if (self::$instCache !== null) return self::$instCache['favicon'];
        self::_loadInstitusi();
        return self::$instCache['favicon'];
    }

    private static function _loadInstitusi() {
        try {
            $db   = Database::connect('core');
            $inst = $db->query("SELECT logo FROM institusi LIMIT 1")->fetch();
            if ($inst && !empty($inst['logo'])) {
                $url = base_url("public/uploads/logo/" . $inst['logo']);
                self::$instCache = ['logo' => $url, 'favicon' => $url];
            } else {
                self::$instCache = [
                    'logo'    => base_url('public/assets/images/logo.png'),
                    'favicon' => base_url('public/assets/images/favicon.png'),
                ];
            }
        } catch (\Exception $e) {
            self::$instCache = [
                'logo'    => base_url('public/assets/images/logo.png'),
                'favicon' => base_url('public/assets/images/favicon.png'),
            ];
        }
    }
}

