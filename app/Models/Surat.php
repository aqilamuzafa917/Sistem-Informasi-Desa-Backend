<?php

namespace App\Models;

use App\Models\Penduduk; 
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Tambahkan Str
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan SoftDeletes

class Surat extends Model
{
    use HasFactory, SoftDeletes; // Tambahkan SoftDeletes trait

    /**
     * Tabel yang terkait dengan model.
     */
    protected $table = 'surats';

    /**
     * Primary key untuk model.
     */
    protected $primaryKey = 'id_surat';

    /**
     * Atribut yang dapat diisi (mass assignable).
     */
    protected $fillable = [
        'nomor_surat',
        'jenis_surat',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'nik_pemohon',
        'keperluan',
        'status_surat',
        'catatan', 
        'attachment_bukti_pendukung',
        
        // SK Kematian
        'nik_penduduk_meninggal',
        'tanggal_kematian',
        'waktu_kematian',
        'tempat_kematian',
        'penyebab_kematian',

        
        // SK Pindah
        'alamat_tujuan',
        'rt_tujuan',
        'rw_tujuan',
        'kelurahan_desa_tujuan',
        'kecamatan_tujuan',
        'kabupaten_kota_tujuan',
        'provinsi_tujuan',
        'alasan_pindah',
        'klasifikasi_pindah',
        'data_pengikut_pindah',
        
        // SK Kelahiran
        'nama_bayi',
        'tempat_dilahirkan',
        'tempat_kelahiran',
        'tanggal_lahir_bayi',
        'waktu_lahir_bayi',
        'jenis_kelamin_bayi',
        'jenis_kelahiran',
        'anak_ke',
        'penolong_kelahiran',
        'berat_bayi_kg',
        'panjang_bayi_cm',
        'nik_penduduk_ibu',
        'nama_ibu',
        'umur_ibu_saat_kelahiran', 
        'nik_penduduk_ayah',
        'nama_ayah', 
        'umur_ayah_saat_kelahiran',

        
        // SK Usaha
        'nama_usaha',
        'jenis_usaha',
        'alamat_usaha',
        'status_bangunan_usaha',
        'perkiraan_modal_usaha',
        'perkiraan_pendapatan_usaha',
        'jumlah_tenaga_kerja',
        'sejak_tanggal_usaha',
        
        // Rekom KIS/KIP/SKTM
        'penghasilan_perbulan_kepala_keluarga',
        'pekerjaan_kepala_keluarga',
        'nik_penduduk_siswa',
        'nama_sekolah',
        'nisn_siswa',
        'kelas_siswa',
        
        // SK Kehilangan KTP
        'nomor_ktp_hilang',
        'tanggal_perkiraan_hilang',
        'lokasi_perkiraan_hilang',
        'kronologi_singkat',
        'nomor_laporan_polisi',
        'tanggal_laporan_polisi',
    ];

    /**
     * Atribut yang harus dikonversi.
     */
    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_disetujui' => 'date',
        // 'tanggal_lahir_pemohon' => 'date',
        'tanggal_kematian' => 'date',
        // 'waktu_kematian' => 'date_format:H:i', 
        // 'tanggal_lahir_meninggal' => 'date',

        'tanggal_lahir_bayi' => 'date',
        // 'waktu_lahir_bayi' => 'date_format:H:i', 
        // 'tanggal_lahir_siswa' => 'date',
        'sejak_tanggal_usaha' => 'date',
        'tanggal_perkiraan_hilang' => 'date',
        'tanggal_laporan_polisi' => 'date',
        'data_pengikut_pindah' => 'array',
        'berat_bayi_kg' => 'decimal:2',
        'panjang_bayi_cm' => 'decimal:2',

        'perkiraan_modal_usaha' => 'integer',
        'perkiraan_pendapatan_usaha' => 'integer',
        'jumlah_tenaga_kerja' => 'integer',
        'penghasilan_perbulan_kepala_keluarga' => 'integer',
        'pekerjaan_kepala_keluarga' => 'string',
        'anak_ke' => 'integer',
        'umur_ibu_saat_kelahiran' => 'integer', // Tambahkan ini
        'umur_ayah_saat_kelahiran' => 'integer', // Tambahkan ini
    ];
 
    protected $hidden = [
        // Sembunyikan objek relasi penuh
        'pemohon',
        'pendudukMeninggal',
        'ibuBayi',
        'ayahBayi',
        'siswa',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // Data terkait Pemohon (dari nik_pemohon)
        'nama_pemohon',
        'tempat_lahir_pemohon',
        'status_perkawinan_pemohon',
        'tanggal_lahir_pemohon',
        'jenis_kelamin_pemohon',
        'alamat_pemohon',
        'umur_pemohon',

        // Data terkait Penduduk Meninggal (dari nik_penduduk_meninggal untuk SK Kematian)
        'nama_meninggal',
        'tempat_lahir_meninggal',
        'tanggal_lahir_meninggal',
        'jenis_kelamin_meninggal',
        'umur_saat_meninggal',
        'nik_meninggal', // Jika Anda ingin NIK ini juga di-append
        'hari_kematian', // Tambahkan ini

        // Data terkait Kelahiran (dari nik_penduduk_ibu, nik_penduduk_ayah,)
        'nama_ibu',
        'umur_ibu_saat_kelahiran',
        'nama_ayah',
        'umur_ayah_saat_kelahiran',

        // Data terkait Siswa (dari nik_penduduk_siswa untuk Rekom KIP/KIS/SKTM)
        'nama_siswa',
        'tempat_lahir_siswa',
        'tanggal_lahir_siswa',
        'jenis_kelamin_siswa',
        'umur_siswa',
        
    ];

    //===========================================================================
    // RELATIONS - Semua relasi ke tabel penduduk untuk data kependudukan
    //===========================================================================

    /**
     * Relasi ke data pemohon di tabel penduduk
     */
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'nik_pemohon', 'nik');
    }

    /**
     * Relasi ke data penduduk yang meninggal (untuk SK Kematian)
     */
    public function pendudukMeninggal(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'nik_penduduk_meninggal', 'nik');
    }

    /**
     * Relasi ke data ibu (untuk SK Kelahiran)
     */
    public function ibuBayi(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'nik_penduduk_ibu', 'nik');
    }

    /**
     * Relasi ke data ayah (untuk SK Kelahiran)
     */
    public function ayahBayi(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'nik_penduduk_ayah', 'nik');
    }

    /**
  
   
     * Relasi ke data siswa (untuk Rekom KIP)
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'nik_penduduk_siswa', 'nik');
    }

    //===========================================================================
    // ACCESSORS - Untuk data pemohon
    //===========================================================================
    
    /**
     * Mengambil nama pemohon dari relasi
     */
    public function getNamaPemohonAttribute()
    {
        if ($this->pemohon) {
            return $this->pemohon->nama;
        }
        
        return $this->attributes['nama_pemohon'] ?? null;
    }

    /**
     * Mengambil tempat lahir pemohon dari relasi
     */
    public function getTempatLahirPemohonAttribute()
    {
        if ($this->pemohon) {
            return $this->pemohon->tempat_lahir;
        }
        
        return $this->attributes['tempat_lahir_pemohon'] ?? null;
    }

    /**
     * Mengambil tanggal lahir pemohon dari relasi
     */
    public function getTanggalLahirPemohonAttribute()
    {
        if ($this->pemohon) {
            return $this->pemohon->tanggal_lahir;
        }
        
        return $this->attributes['tanggal_lahir_pemohon'] ?? null;
    }

    /**
     * Mengambil jenis kelamin pemohon dari relasi
     */
    public function getJenisKelaminPemohonAttribute()
    {
        if ($this->pemohon) {
            return $this->pemohon->jenis_kelamin;
        }
        
        return $this->attributes['jenis_kelamin_pemohon'] ?? null;
    }

    /**
     * Mengambil alamat pemohon dari relasi
     */
    public function getAlamatPemohonAttribute()
    {
        if ($this->pemohon) {
            return $this->pemohon->alamat;
        }
        
        return null;
    }

    /**
     * Mengambil umur pemohon dari relasi
     */
    public function getUmurPemohonAttribute()
    {
        if ($this->pemohon) {
            return Carbon::parse($this->pemohon->tanggal_lahir)->age;
        }
        
        if (isset($this->attributes['tanggal_lahir_pemohon'])) {
            return Carbon::parse($this->attributes['tanggal_lahir_pemohon'])->age;
        }
        
        return null;
    }
    
    public function getStatusPerkawinanPemohonAttribute()
    {
        if ($this->pemohon) {
            return $this->pemohon->status_perkawinan;
        }
        
        return null;
    }

    //===========================================================================
    // ACCESSORS - Untuk data penduduk yang meninggal (SK Kematian)
    //===========================================================================
    
    /**
     * Mengambil nama penduduk yang meninggal dari relasi
     */
    public function getNamaMeninggalAttribute()
    {
        if ($this->pendudukMeninggal) {
            return $this->pendudukMeninggal->nama;
        }
        
        return $this->attributes['nama_meninggal'] ?? null;
    }

    /**
     * Mengambil tempat lahir penduduk yang meninggal dari relasi
     */
    public function getTempatLahirMeninggalAttribute()
    {
        if ($this->pendudukMeninggal) {
            return $this->pendudukMeninggal->tempat_lahir;
        }
        
        return $this->attributes['tempat_lahir_meninggal'] ?? null;
    }

    /**
     * Mengambil tanggal lahir penduduk yang meninggal dari relasi
     */
    public function getTanggalLahirMeninggalAttribute()
    {
        if ($this->pendudukMeninggal) {
            return $this->pendudukMeninggal->tanggal_lahir;
        }
        
        return $this->attributes['tanggal_lahir_meninggal'] ?? null;
    }

    /**
     * Mengambil jenis kelamin penduduk yang meninggal dari relasi
     */
    public function getJenisKelaminMeninggalAttribute()
    {
        if ($this->pendudukMeninggal) {
            return $this->pendudukMeninggal->jenis_kelamin;
        }
        
        return $this->attributes['jenis_kelamin_meninggal'] ?? null;
    }

    /**
     * Mengambil umur penduduk yang meninggal saat meninggal
     */
    public function getUmurSaatMeninggalAttribute()
    {
        if (!isset($this->attributes['tanggal_kematian'])) return null;
        
        if ($this->pendudukMeninggal) {
            return Carbon::parse($this->pendudukMeninggal->tanggal_lahir)
                ->diffInYears(Carbon::parse($this->tanggal_kematian));
        }
        
        if (isset($this->attributes['tanggal_lahir_meninggal'])) {
            return Carbon::parse($this->attributes['tanggal_lahir_meninggal'])
                ->diffInYears(Carbon::parse($this->tanggal_kematian));
        }
        
        return null;
    }

    /**
     * Mengambil hari kematian dari tanggal kematian.
     */
    public function getHariKematianAttribute()
    {
        if (!isset($this->attributes['tanggal_kematian'])) {
            return null;
        }
        
        // Pastikan Carbon menggunakan lokal Indonesia untuk nama hari
        return Carbon::parse($this->attributes['tanggal_kematian'])->locale('id_ID')->dayName;
    }

    /**
     * Mengambil NIK penduduk yang meninggal dalam format string
     */
    public function getNikMeninggalAttribute()
    {
        if ($this->pendudukMeninggal) {
            return $this->pendudukMeninggal->nik;
        }
        
        return $this->attributes['nik_meninggal'] ?? $this->attributes['nik_penduduk_meninggal'] ?? null;
    }

    //===========================================================================
    // ACCESSORS - Untuk data siswa (Rekom KIP)
    //===========================================================================
    
    /**
     * Mengambil nama siswa dari relasi
     */
    public function getNamaSiswaAttribute()
    {
        if ($this->siswa) {
            return $this->siswa->nama;
        }
        
        return $this->attributes['nama_siswa'] ?? null;
    }

    /**
     * Mengambil tempat lahir siswa dari relasi
     */
    public function getTempatLahirSiswaAttribute()
    {
        if ($this->siswa) {
            return $this->siswa->tempat_lahir;
        }
        
        return $this->attributes['tempat_lahir_siswa'] ?? null;
    }

    /**
     * Mengambil tanggal lahir siswa dari relasi
     */
    public function getTanggalLahirSiswaAttribute()
    {
        if ($this->siswa) {
            return $this->siswa->tanggal_lahir;
        }
        
        return $this->attributes['tanggal_lahir_siswa'] ?? null;
    }

    /**
     * Mengambil jenis kelamin siswa dari relasi
     */
    public function getJenisKelaminSiswaAttribute()
    {
        if ($this->siswa) {
            return $this->siswa->jenis_kelamin;
        }
        
        return $this->attributes['jenis_kelamin_siswa'] ?? null;
    }

    /**
     * Mengambil umur siswa saat ini
     */
    public function getUmurSiswaAttribute()
    {
        if ($this->siswa) {
            return Carbon::parse($this->siswa->tanggal_lahir)->age;
        }
        
        if (isset($this->attributes['tanggal_lahir_siswa'])) {
            return Carbon::parse($this->attributes['tanggal_lahir_siswa'])->age;
        }
        
        return null;
    }

    //===========================================================================
    // ACCESSORS - Untuk data orang tua bayi (SK Kelahiran)
    //===========================================================================
    
    /**
     * Mengambil nama ibu dari relasi atau atribut langsung.
     */
    public function getNamaIbuAttribute()
    {
        if ($this->ibuBayi) {
            return $this->ibuBayi->nama;
        }
        // Fallback ke atribut 'nama_ibu' jika relasi tidak ada atau tidak di-load
        return $this->attributes['nama_ibu'] ?? null;
    }

    /**
     * Mengambil umur ibu saat kelahiran, prioritaskan atribut langsung jika ada.
     */
    public function getUmurIbuSaatKelahiranAttribute()
    {
        // Prioritaskan nilai dari atribut jika sudah diisi (misalnya dari input manual)
        if (isset($this->attributes['umur_ibu_saat_kelahiran']) && $this->attributes['umur_ibu_saat_kelahiran'] !== null) {
            return (int) $this->attributes['umur_ibu_saat_kelahiran'];
        }
        
        // Jika tidak ada nilai di atribut, coba hitung dari relasi
        if ($this->ibuBayi && isset($this->attributes['tanggal_lahir_bayi'])) {
            // Pastikan tanggal lahir ibu ada di relasi
            if ($this->ibuBayi->tanggal_lahir) {
                 return Carbon::parse($this->ibuBayi->tanggal_lahir)
                    ->diffInYears(Carbon::parse($this->attributes['tanggal_lahir_bayi']));
            }
        }
        
        return null;
    }

    /**
     * Mengambil nama ayah dari relasi atau atribut langsung.
     */
    public function getNamaAyahAttribute()
    {
        if ($this->ayahBayi) {
            return $this->ayahBayi->nama;
        }
        // Fallback ke atribut 'nama_ayah' jika relasi tidak ada atau tidak di-load
        return $this->attributes['nama_ayah'] ?? null;
    }

    /**
     * Mengambil umur ayah saat kelahiran, prioritaskan atribut langsung jika ada.
     */
    public function getUmurAyahSaatKelahiranAttribute()
    {
        // Prioritaskan nilai dari atribut jika sudah diisi
        if (isset($this->attributes['umur_ayah_saat_kelahiran']) && $this->attributes['umur_ayah_saat_kelahiran'] !== null) {
            return (int) $this->attributes['umur_ayah_saat_kelahiran'];
        }

        // Jika tidak ada nilai di atribut, coba hitung dari relasi
        if ($this->ayahBayi && isset($this->attributes['tanggal_lahir_bayi'])) {
            // Pastikan tanggal lahir ayah ada di relasi
            if ($this->ayahBayi->tanggal_lahir) {
                return Carbon::parse($this->ayahBayi->tanggal_lahir)
                    ->diffInYears(Carbon::parse($this->attributes['tanggal_lahir_bayi']));
            }
        }
        
        return null;
    }


   

    //===========================================================================
    // MUTATORS & UTILITY METHODS 
    //===========================================================================

    /**
     * Menghasilkan nomor surat otomatis berdasarkan jenis dan tanggal
     */
    private static function getKodeKlasifikasi(string $jenisSurat): string
    {
        // Normalisasi key untuk konsistensi (opsional, tapi membantu)
        $jenisSuratKey = strtoupper(str_replace(' ', '_', trim($jenisSurat)));

        switch ($jenisSuratKey) {
            // --- Kependudukan & Catatan Sipil (470) ---
            case 'SK_DOMISILI': return '471';
            case 'SK_KEMATIAN': return '472.12';
            case 'SK_PINDAH': return '471';
            case 'SK_KELAHIRAN': return '472.11';
            case 'SK_KEHILANGAN_KTP': return '471';
            case 'SK_KEHILANGAN_KK': return '471';

            // --- Kesejahteraan Rakyat & Sosial (400, 460) ---
            case 'SK_TIDAK_MAMPU': // atau SKTM
            case 'SKTM':
                return '401';
            case 'REKOM_KIP': // atau KARTU_INDONESIA_PINTAR
            case 'KARTU_INDONESIA_PINTAR':
                 return '422.5';
            case 'REKOM_KIS': // atau KARTU_INDONESIA_SEHAT
            case 'KARTU_INDONESIA_SEHAT':
                 return '440';
            case 'REKOM_SUBSIDI_LISTRIK': // atau SUBSIDI_LISTRIK
            case 'SUBSIDI_LISTRIK':
                 return '401';

            // --- Ekonomi & Administrasi Desa (500, 140) ---
            case 'SK_USAHA': return '581'; // Atau 500/510 jika lebih sesuai

            // --- Umum ---
            case 'LAIN_LAIN':
            case 'UMUM': // Tambahkan alias jika perlu
            case 'UMUM_DEFAULT': // Untuk fallback di boot()
                return '000';

            // --- Tambahkan jenis surat lain jika ada ---
            case 'UNDANGAN': return '005';
            case 'PENGUMUMAN': return '000'; // atau lebih spesifik

            default:
                // Fallback jika jenis surat tidak dikenal
                // error_log("Peringatan: Jenis surat '$jenisSurat' tidak memiliki kode klasifikasi yang dipetakan.");
                return '000'; // Kode Umum
        }
    }

    /**
     * Mendapatkan Prefix singkat berdasarkan jenis surat.
     *
     * @param string $jenisSurat Key unik untuk jenis surat
     * @return string Prefix yang sesuai
     */
    private static function getPrefix(string $jenisSurat): string
     {
        // Normalisasi key
        $jenisSuratKey = strtoupper(str_replace(' ', '_', trim($jenisSurat)));

        switch ($jenisSuratKey) {
            case 'SK_DOMISILI': return 'DOM';
            case 'SK_KEMATIAN': return 'KMT';
            case 'SK_PINDAH': return 'PND';
            case 'SK_KELAHIRAN': return 'LHR';
            case 'SK_USAHA': return 'USH';
            case 'REKOM_KIP':
            case 'KARTU_INDONESIA_PINTAR':
                 return 'KIP';
            case 'REKOM_KIS':
            case 'KARTU_INDONESIA_SEHAT':
                 return 'KIS';
            case 'SK_TIDAK_MAMPU':
            case 'SKTM':
                 return 'SKTM'; 
            case 'SK_KEHILANGAN_KTP': return 'HKTP';
            case 'SK_KEHILANGAN_KK': return 'HKK';
            case 'REKOM_SUBSIDI_LISTRIK':
            case 'SUBSIDI_LISTRIK':
                 return 'SUB'; 
            case 'LAIN_LAIN':
            case 'UMUM':
            case 'UMUM_DEFAULT':
                 return 'SRT'; // Surat Umum
            case 'UNDANGAN': return 'UND';
            case 'PENGUMUMAN': return 'PENG';
            // Tambahkan prefix lain jika perlu
            default:
                return 'SRT'; // Default Prefix
        }
     }

     /**
      * Helper untuk konversi bulan angka ke Romawi.
      *
      * @param int $bulan Angka bulan (1-12)
      * @return string Representasi Romawi atau string kosong jika tidak valid
      */
     private static function getBulanRomawi(int $bulan): string
     {
         $romawi = [
             1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
             7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
         ];
         return $romawi[$bulan] ?? ''; // Fallback jika bulan tidak valid
     }

    /**
     * Generate Nomor Surat Otomatis berdasarkan Klasifikasi dan format yang ditentukan.
     * Format: KodeKlasifikasi / NomorUrut / Prefix / KodeDesa / BulanRomawi / Tahun
     *
     * @param string $jenisSurat Key Jenis surat (e.g., 'SK_KEMATIAN')
     * @param string $modelClass Nama class model ini (biasanya self::class)
     * @return string Nomor surat yang digenerate
     */
    public static function generateNomorSurat(string $jenisSurat, string $modelClass = self::class): string
    {
        $kodeKlasifikasi = self::getKodeKlasifikasi($jenisSurat);
        $prefix = self::getPrefix($jenisSurat);

        $now = Carbon::now();
        $tahun = $now->year;
        $bulan = $now->month;
        $bulanRomawi = self::getBulanRomawi($bulan);

        // Hitung jumlah surat yang sudah disetujui (status 'Disetujui') pada bulan dan tahun ini
        // Perubahan: Hanya menghitung surat dengan status 'Disetujui'
        $count = $modelClass::whereYear('created_at', $tahun)
                           ->whereMonth('created_at', $bulan)
                           ->where('status_surat', 'Disetujui')
                           ->count() + 1; // Nomor urut surat berikutnya
        
        // Mengambil Kode Desa dari file konfigurasi
        $kodeDesa = config('desa.kode', 'KODE_ERROR'); // Perbaikan: Tambahkan 'KODE_INVALID' sebagai default
        
        // Format Final: KodeKlasifikasi / NomorUrut / Prefix / KodeDesa / BulanRomawi / Tahun
        // Contoh: 472.12/001/KMT/DS.2012/IV/2024
        return sprintf(
            '%s/%03d/%s/%s/%s/%d', // Format: string / 3-digit-int / string / string / string / int
            $kodeKlasifikasi,
            $count, // Nomor urut dipad dengan 0 hingga 3 digit
            $prefix,
            $kodeDesa,
            $bulanRomawi,
            $tahun
        );
    }

    /**
     * Boot the model.
     * Mendaftarkan event listener untuk mengisi nomor surat saat record baru dibuat.
     */
    protected static function boot()
    {
        parent::boot(); // Jangan lupa panggil parent boot

        // Hapus event creating yang menggenerate nomor surat
        // Nomor surat akan digenerate saat approval
    }

    
    /**
     * Method untuk menyetujui surat
     */
    public function approve()
    {
        if ($this->status_surat !== 'Disetujui') {
            // Generate nomor surat saat approval
            if (empty($this->nomor_surat)) {
                $this->nomor_surat = self::generateNomorSurat($this->jenis_surat);
            }
            
            $this->status_surat = 'Disetujui';
            $this->tanggal_disetujui = Carbon::now()->toDateString();
            $this->save();
            return true;
        }
        
        return false;
    }
    
    /**
     * Method untuk menolak surat
     */
    public function reject()
    {
        if ($this->status_surat !== 'Ditolak') {
            $this->status_surat = 'Ditolak';
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * Method untuk menandai surat sudah dicetak
     */
    public function markAsPrinted()
    {
        if ($this->status_surat === 'Disetujui') {
            $this->status_surat = 'Printed';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Method untuk mendapatkan alamat lengkap tujuan pindah
     */
    public function getAlamatLengkapTujuanAttribute()
    {
        if (empty($this->alamat_tujuan)) return null;
        
        $parts = [];
        
        $parts[] = $this->alamat_tujuan;
        
        if (!empty($this->rt_tujuan) && !empty($this->rw_tujuan)) {
            $parts[] = "RT {$this->rt_tujuan}/RW {$this->rw_tujuan}";
        }
        
        if (!empty($this->kelurahan_desa_tujuan)) {
            $parts[] = "Kel/Desa {$this->kelurahan_desa_tujuan}";
        }
        
        if (!empty($this->kecamatan_tujuan)) {
            $parts[] = "Kec. {$this->kecamatan_tujuan}";
        }
        
        if (!empty($this->kabupaten_kota_tujuan)) {
            $parts[] = $this->kabupaten_kota_tujuan;
        }
        
        if (!empty($this->provinsi_tujuan)) {
            $parts[] = "Provinsi {$this->provinsi_tujuan}";
        }
        
        return implode(', ', $parts);
    }

    /**
     * Mendapatkan umur bayi saat ini (untuk SK_KELAHIRAN)
     */
    public function getUmurBayiAttribute()
    {
        if (isset($this->attributes['tanggal_lahir_bayi'])) {
            return Carbon::parse($this->attributes['tanggal_lahir_bayi'])->age;
        }
        
        return null;
    }
    
    /**
     * Mengambil format tempat dan tanggal lahir yang lengkap
     */
    public function getTempatTanggalLahirLengkapAttribute()
    {
        $tempatLahir = $this->getTempatLahirPemohonAttribute();
        $tanggalLahir = $this->getTanggalLahirPemohonAttribute();
        
        if ($tempatLahir && $tanggalLahir) {
            return $tempatLahir . ', ' . Carbon::parse($tanggalLahir)->format('d F Y');
        }
        
        return null;
    }
    
    /**
     * Scope query untuk filter berdasarkan jenis surat
     */
    public function scopeJenisSurat($query, $jenisSurat)
    {
        return $query->where('jenis_surat', $jenisSurat);
    }
    
    /**
     * Scope query untuk filter berdasarkan status surat
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status_surat', $status);
    }
    
    /**
     * Scope query untuk filter berdasarkan pemohon
     */
    public function scopePemohon($query, $nikPemohon)
    {
        return $query->where('nik_pemohon', $nikPemohon);
    }
    
    /**
     * Scope query untuk filter tanggal request
     */
    public function scopeTanggalRequest($query, $start, $end = null)
    {
        if ($end) {
            return $query->whereBetween('tanggal_pengajuan', [$start, $end]);
        }
        
        return $query->whereDate('tanggal_pengajuan', $start);
    }
    
    /**
     * Scope query untuk surat yang belum diproses
     */
    public function scopeBelumDiproses($query)
    {
        return $query->where('status_surat', 'Diajukan');
    }


    /**
     * Mutator untuk mengatur data pengikut pindah
     */
    public function setDataPengikutPindahAttribute($value)
    {
        if (is_array($value)) {
            // Pastikan setiap pengikut memiliki data lengkap jika NIK tersedia
            foreach ($value as $key => $pengikut) {
                if (isset($pengikut['nik']) && !empty($pengikut['nik'])) {
                    $penduduk = Penduduk::find($pengikut['nik']);
                    
                    if ($penduduk) {
                        // Update data dari tabel penduduk
                        $value[$key]['nama'] = $penduduk->nama;
                        $value[$key]['tempat_lahir'] = $penduduk->tempat_lahir;
                        $value[$key]['tanggal_lahir'] = $penduduk->tanggal_lahir;
                        $value[$key]['jenis_kelamin'] = $penduduk->jenis_kelamin;
                        $value[$key]['status_perkawinan'] = $penduduk->status_perkawinan;
                    }
                }
            }
        }
        
        $this->attributes['data_pengikut_pindah'] = is_array($value) ? json_encode($value) : $value;
    }
}
