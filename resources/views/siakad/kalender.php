<?php 
// Calculate calendar data
$bulan_indo_names = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$monthName = $bulan_indo_names[(int)$month] . ' ' . $year;
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = date('N', strtotime("$year-$month-01")); // 1 (Mon) - 7 (Sun)
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth == 0) { $prevMonth = 12; $prevYear--; }
$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth == 13) { $nextMonth = 1; $nextYear++; }

// Map agendas by date
$agendaMap = [];
foreach ($agendas as $a) {
    $current = strtotime($a['tanggal_mulai']);
    $end = strtotime($a['tanggal_selesai']);
    while ($current <= $end) {
        $d = date('Y-m-d', $current);
        if (!isset($agendaMap[$d])) $agendaMap[$d] = [];
        $agendaMap[$d][] = $a;
        $current = strtotime('+1 day', $current);
    }
}
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="z-main">
        <?php include __DIR__ . '/_siakad_header.php'; ?>

        <div class="z-scroll" style="padding: 2rem;">
            <!-- Elite Header -->
            <div class="elite-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); padding: 2rem 2.5rem; border-radius: 12px; margin-bottom: 2rem; color: white; position: relative; overflow: hidden;">
                <div class="header-bg-shapes" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background-image: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 40%), radial-gradient(circle at 20% 80%, rgba(0, 0, 0, 0.1) 0%, transparent 40%); z-index: 1;"></div>
                <div style="position: absolute; inset: 0; opacity: 0.15; pointer-events: none; background: url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1.5\' fill=\'white\' /%3E%3C/svg%3E'); z-index: 1;"></div>
                <div style="position: relative; z-index: 2;">
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px;">
                        <i data-lucide="calendar-days" style="color: rgba(255,255,255,0.8);"></i> <?php echo isset($page_title) ? $page_title : 'Kalender Akademik'; ?>
                    </h1>
                    <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 0.95rem;">Agenda, Hari Libur, dan Kegiatan Sekolah Sepanjang Tahun Ajaran.</p>
                </div>
            </div>

            <!-- Modern Calendar Interface -->
            <div class="z-card" id="calendar-wrapper" style="padding: 1.5rem 2rem; border-radius: 16px;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.2rem; font-weight: 800; color: #1e293b; margin: 0;"><?php echo $monthName . ' ' . $year; ?></h2>
                        <p style="color: #64748b; margin: 2px 0 0 0; font-size: 0.8rem;">Tahun Ajaran <?php echo isset($ta['name']) ? $ta['name'] : '-'; ?></p>
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <button onclick="changeMonth(<?php echo $prevMonth; ?>, <?php echo $prevYear; ?>)" class="z-btn z-btn-ghost" style="padding: 6px 12px; border-radius: 8px;">
                            <i data-lucide="chevron-left" style="width:16px;"></i> Sebelumnya
                        </button>
                        <button onclick="changeMonth(<?php echo $nextMonth; ?>, <?php echo $nextYear; ?>)" class="z-btn z-btn-ghost" style="padding: 6px 12px; border-radius: 8px;">
                            Berikutnya <i data-lucide="chevron-right" style="width:16px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Minimalist List Layout -->
                <div style="display: flex; flex-direction: column; gap: 0; margin-bottom: 1rem;">
                    <?php 
                    $hasAgenda = false;
                    for($i=1; $i<=$daysInMonth; $i++): 
                        $dayOfWeek = date('N', strtotime("$year-$month-$i"));
                        $isWeekend = ($dayOfWeek == 7);
                        $currentDateStr = sprintf("%04d-%02d-%02d", $year, $month, $i);
                        $dayAgendas = isset($agendaMap[$currentDateStr]) ? $agendaMap[$currentDateStr] : [];
                        $isToday = ($currentDateStr == date('Y-m-d'));
                        
                        if(count($dayAgendas) > 0):
                            $hasAgenda = true;
                    ?>
                        <div style="padding: 1rem 0; border-bottom: 1px solid #f1f5f9; display: flex; gap: 1.5rem; align-items: center;">
                            <div style="flex-shrink: 0; width: 60px; text-align: center;">
                                <div style="font-size: 0.7rem; font-weight: 600; color: #94a3b8; text-transform: uppercase;"><?php 
                                    $nama_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                    echo $nama_hari[$dayOfWeek - 1]; 
                                ?></div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: <?php echo $isWeekend ? '#ef4444' : ($isToday ? '#0ea5e9' : '#1e293b'); ?>; line-height: 1.1; margin-top: 2px;"><?php echo $i; ?></div>
                            </div>
                            <div style="flex-grow: 1; display: flex; flex-direction: column; gap: 8px;">
                                <?php foreach($dayAgendas as $ag): 
                                    if($ag['tipe'] == 'akademik') { $dotColor = "#3b82f6"; }
                                    if($ag['tipe'] == 'libur') { $dotColor = "#ef4444"; }
                                    if($ag['tipe'] == 'kegiatan') { $dotColor = "#22c55e"; }
                                    if($ag['tipe'] == 'pulang_awal') { $dotColor = "#eab308"; }
                                    if($ag['tipe'] == 'daring') { $dotColor = "#a855f7"; }
                                ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; background: #f8fafc; border-radius: 6px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo $dotColor; ?>;"></span>
                                            <span style="font-weight: 500; font-size: 0.9rem; color: #334155;"><?php echo htmlspecialchars($ag['keterangan']); ?></span>
                                        </div>
                                        <button class="z-btn z-btn-ghost" style="padding: 4px; color: #94a3b8; opacity: 0.7;" onclick="deleteAgenda(event, <?php echo $ag['id']; ?>)" title="Hapus">
                                            <i data-lucide="trash-2" style="width: 14px;"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endfor; 
                    
                    if(!$hasAgenda):
                    ?>
                        <div style="text-align: center; padding: 2.5rem; color: #94a3b8; font-size: 0.9rem;">
                            Tidak ada agenda di bulan ini.
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 20px; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Keterangan:</span>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #0ea5e9;"></span> Akademik Normal
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #22c55e;"></span> Kegiatan
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #eab308;"></span> Pulang Awal / Rapat
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span> Libur
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #a855f7;"></span> Daring (PJJ)
                    </div>
                    
                    <div style="margin-left: auto;">
                        <button class="z-btn z-btn-primary" onclick="openAddAgendaModal()" style="padding: 8px 16px;">
                            <i data-lucide="plus" style="width:16px;"></i> Tambah Agenda
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Agenda -->
<div class="z-modal" id="modal-agenda">
    <div class="z-modal-content small">
        <div class="z-modal-header">
            <h3><i data-lucide="calendar-plus"></i> Form Agenda</h3>
            <button type="button" class="close-btn" onclick="document.getElementById('modal-agenda').style.display='none'"><i data-lucide="x"></i></button>
        </div>
        <div class="z-modal-body">
            <form action="<?php echo base_url('admin/siakad/kalender/save'); ?>" method="POST" class="z-form-vertical" id="agenda-form">
                <input type="hidden" name="id" id="agenda-id">
                
                <div class="form-group">
                    <label style="color: #334155; font-weight: 600; margin-bottom: 5px; display: block;">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="agenda-mulai" class="z-input" required>
                </div>
                
                <div class="form-group">
                    <label style="color: #334155; font-weight: 600; margin-bottom: 5px; display: block;">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="agenda-selesai" class="z-input" required>
                </div>
                
                <div class="form-group">
                    <label style="color: #334155; font-weight: 600; margin-bottom: 5px; display: block;">Tipe Hari</label>
                    <select name="tipe" id="agenda-tipe" class="z-input" onchange="toggleJamKosong()" required>
                        <option value="akademik">Akademik Normal</option>
                        <option value="kegiatan">Kegiatan (Wajib Scan, Bebas Jurnal/Absen Kelas)</option>
                        <option value="pulang_awal">Pulang Awal / Rapat (Bebas Jurnal Setelah Jam Tertentu)</option>
                        <option value="libur">Libur (Bebas Semua Absen)</option>
                        <option value="daring">Daring (PJJ)</option>
                    </select>
                </div>
                
                <div class="form-group" id="group-jam-kosong" style="display: none;">
                    <label style="color: #334155; font-weight: 600; margin-bottom: 5px; display: block;">Jam Kepulangan / Mulai Kosong</label>
                    <input type="time" name="jam_mulai_kosong" id="agenda-jam-kosong" class="z-input">
                    <small style="color:#64748b; font-size: 0.75rem;">Jadwal Jurnal dan Mesin Scanner di atas jam ini akan dibebaskan.</small>
                </div>
                
                <div class="form-group">
                    <label style="color: #334155; font-weight: 600; margin-bottom: 5px; display: block;">Keterangan / Nama Kegiatan</label>
                    <input type="text" name="keterangan" class="z-input" required placeholder="Contoh: Libur Idul Fitri">
                </div>
            </form>
        </div>
        <div class="z-modal-footer">
            <button type="button" class="z-btn z-btn-ghost" onclick="document.getElementById('modal-agenda').style.display='none'">Batal</button>
            <button type="submit" form="agenda-form" class="z-btn z-btn-primary"><i data-lucide="save"></i> Simpan Agenda</button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    function toggleJamKosong() {
        var tipe = document.getElementById('agenda-tipe').value;
        if (tipe === 'pulang_awal') {
            document.getElementById('group-jam-kosong').style.display = 'block';
            document.getElementById('agenda-jam-kosong').setAttribute('required', 'required');
        } else {
            document.getElementById('group-jam-kosong').style.display = 'none';
            document.getElementById('agenda-jam-kosong').removeAttribute('required');
        }
    }
    
    function openAddAgendaModal(dateStr = '') {
        document.getElementById('agenda-form').reset();
        document.getElementById('agenda-id').value = '';
        toggleJamKosong();
        if(dateStr) {
            document.getElementById('agenda-mulai').value = dateStr;
            document.getElementById('agenda-selesai').value = dateStr;
        }
        document.getElementById('modal-agenda').style.display = 'flex';
    }
    
    function deleteAgenda(e, id) {
        e.stopPropagation();
        ZNS.confirm('Apakah Anda yakin ingin menghapus agenda ini?', () => {
            window.location.href = '<?php echo base_url('admin/siakad/kalender/delete/'); ?>' + id;
        });
    }
    
    function changeMonth(month, year) {
        const wrapper = document.getElementById('calendar-wrapper');
        wrapper.style.opacity = '0.5';
        wrapper.style.pointerEvents = 'none';
        
        fetch(`?month=${month}&year=${year}`)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newWrapper = doc.getElementById('calendar-wrapper');
                if(newWrapper) {
                    wrapper.innerHTML = newWrapper.innerHTML;
                    wrapper.style.opacity = '1';
                    wrapper.style.pointerEvents = 'auto';
                    lucide.createIcons();
                    // Update URL silently
                    window.history.pushState({}, '', `?month=${month}&year=${year}`);
                }
            })
            .catch(err => {
                console.error(err);
                window.location.href = `?month=${month}&year=${year}`;
            });
    }
    
    <?php if(isset($_GET['zns'])): ?>
    setTimeout(() => {
        ZNS.showSuccess('<?php echo htmlspecialchars($_GET['zns']); ?>');
        window.history.replaceState({}, document.title, window.location.pathname);
    }, 100);
    <?php endif; ?>
</script>
