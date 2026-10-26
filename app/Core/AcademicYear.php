<?php

namespace App\Core;

class AcademicYear {
    /**
     * Get the active or session-selected academic year
     */
    public static function current() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $dbCore = Database::connect('core');
        
        // If user manually switched the context in their session
        if (isset($_SESSION['selected_tahun_ajaran_id'])) {
            $id = $_SESSION['selected_tahun_ajaran_id'];
            $stmt = $dbCore->prepare("SELECT * FROM tahun_ajaran WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $year = $stmt->fetch();
            if ($year) {
                return $year;
            }
        }

        // Fallback to SIAKAD's global setting
        $dbSiakad = Database::connect('siakad');
        $pengaturan = $dbSiakad->query("SELECT tahun_ajaran_aktif, semester_aktif FROM pengaturan LIMIT 1")->fetch();
        
        if ($pengaturan) {
            $name = $pengaturan['tahun_ajaran_aktif'];
            $semesterName = strtolower(trim($pengaturan['semester_aktif']));
            $semesterNum = ($semesterName === 'genap' || $semesterName === '2') ? 2 : 1;
            
            // Sync with core_db.tahun_ajaran to get an ID
            $stmt = $dbCore->prepare("SELECT * FROM tahun_ajaran WHERE name = ? AND semester = ? LIMIT 1");
            $stmt->execute([$name, $semesterNum]);
            $activeYear = $stmt->fetch();
            
            if ($activeYear) {
                // Ensure it is marked as active in core_db
                $dbCore->prepare("UPDATE tahun_ajaran SET is_active = 0")->execute();
                $dbCore->prepare("UPDATE tahun_ajaran SET is_active = 1 WHERE id = ?")->execute([$activeYear['id']]);
                return $activeYear;
            } else {
                // Insert it into core_db if it doesn't exist yet
                $dbCore->prepare("UPDATE tahun_ajaran SET is_active = 0")->execute();
                $stmtIns = $dbCore->prepare("INSERT INTO tahun_ajaran (name, semester, is_active) VALUES (?, ?, 1)");
                $stmtIns->execute([$name, $semesterNum]);
                $newId = $dbCore->lastInsertId();
                return [
                    'id' => $newId,
                    'name' => $name,
                    'semester' => $semesterNum,
                    'is_active' => 1
                ];
            }
        }

        // Ultimate fallback
        return [
            'id' => 0,
            'name' => 'Belum Diatur',
            'semester' => 1,
            'is_active' => 1
        ];
    }

    /**
     * Get all available academic years for the switcher dropdown
     */
    public static function getAll() {
        $db = Database::connect('core');
        return $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC, semester DESC")->fetchAll();
    }
}
