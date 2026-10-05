# Plan: Perbaiki Persentase di Pie Chart (Tampilkan % Saja)

## Ringkasan
Memperbaiki pie chart agar persentase **muncul** dan **hanya menampilkan persentase** (bukan nama kandidat + persentase) di setiap slice pie.

## Analisis Current State

### Masalah yang Diidentifikasi

#### 1. Format Label Saat Ini (Tidak Sesuai Permintaan User)
**File:** `resources/views/dashboard/components/pie.blade.php` baris 62-67
```javascript
formatter: function(value, context) {
    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
    const percentage = ((value / total) * 100).toFixed(1);
    const label = context.chart.data.labels[context.dataIndex];
    return label + '\n' + percentage + '%';  // ← Nama + Persentase
}
```
**Masalah:** User eksplisit bilang "tampilkan persentase saja bukan sama namanya" → format saat ini masih include nama kandidat

#### 2. Potensi Masalah Load Order Plugin
**File:** `resources/views/dashboard/layouts/main.blade.php` baris 99-105
```
99:  chart-utils.js
102: chart.umd.min.js (Chart.js core)
105: chartjs-plugin-datalabels@2.2.0 (CDN)
```
**Masalah potensial:** `pie.blade.php` baris 74 memanggil `Chart.register(ChartDataLabels)`. Jika script pie.blade.php dieksekusi sebelum CDN plugin selesai di-load, `ChartDataLabels` akan `undefined` dan label tidak muncul

#### 3. Chart.register(ChartDataLabels) Global
**File:** `pie.blade.php` baris 74
```javascript
Chart.register(ChartDataLabels);
```
**Masalah:** Registrasi plugin dilakukan di dalam `$(document).ready()` pie.blade.php. Jika plugin belum ter-load (race condition CDN), `ChartDataLabels` undefined → error silently atau label tidak tampil. Solusi: pindahkan registrasi setelah CDN plugin dipastikan load, atau pakai defer/await.

#### 4. JSDoc chart-utils.js Belum Di-update
**File:** `public/assets/js/chart-utils.js` baris 13-15, 21, 26
- Komentar masih bilang `default 0.2`, `default 70`, `default 60` padahal nilai sudah berubah ke 0.7, 85, 55
- Bukan masalah fungsional, tapi misinformasi untuk developer

## Proposed Changes

### 1. File: `resources/views/dashboard/components/pie.blade.php`
**Apa yang diubah:**
- Ubah format `datalabels.formatter` untuk return **hanya persentase** (tanpa nama kandidat)
- Pindahkan `Chart.register(ChartDataLabels)` ke tempat yang aman (guard check)

**Mengapa:**
- User eksplisit minta "tampilkan persentase saja bukan sama namanya"
- Guard check untuk `ChartDataLabels` mencegah error jika plugin belum ter-load saat script jalan

**Bagaimana:**
```javascript
// Ganti formatter (baris 62-67)
formatter: function(value, context) {
    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
    const percentage = ((value / total) * 100).toFixed(1);
    return percentage + '%';  // ← Hanya persentase, tanpa nama
}

// Guard check registrasi plugin (baris 73-74)
if (typeof ChartDataLabels !== 'undefined') {
    Chart.register(ChartDataLabels);
}
```

**Lokasi:** Baris 62-67 (formatter) dan baris 73-74 (registrasi)

### 2. File: `resources/views/dashboard/layouts/main.blade.php`
**Apa yang diubah:**
- Tambahkan `defer` attribute pada script CDN datalabels untuk pastikan load setelah DOM ready

**Mengapa:**
- `defer` memastikan script CDN di-download paralel tapi dieksekusi setelah HTML parsing selesai
- Mencegah race condition antara CDN load dan script pie.blade.php yang memanggil `ChartDataLabels`

**Bagaimana:**
```html
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js" defer></script>
```

**Lokasi:** Baris 105

### 3. File: `public/assets/js/chart-utils.js` (Cleanup)
**Apa yang diubah:**
- Update JSDoc comment untuk reflect nilai default yang baru (0.7, 85, 55)

**Mengapa:**
- Konsistensi dokumentasi dengan implementasi
- Mencegah misinformasi developer

**Bagaimana:**
```javascript
 * @param {number} options.saturation - Saturation value (0-100), default 85
 * @param {number} options.lightness - Lightness value (0-100), default 55
 * @param {number} options.opacity - Opacity untuk background (0-1), default 0.7
```

**Lokasi:** Baris 13-15, 21, 26

## Assumptions & Decisions

### Asumsi Teknis
1. **CDN accessible**: User punya internet untuk load `cdn.jsdelivr.net/npm/chartjs-plugin-datalabels`
2. **Chart.js v3+**: Plugin datalabels v2.2.0 kompatibel dengan Chart.js core yang dipakai
3. **Race condition root cause**: `ChartDataLabels` undefined saat `Chart.register()` dipanggil

### Keputusan Desain
1. **Format persentase saja**: `"45.2%"` (tanpa nama kandidat)
   - User eksplisit minta ini
   - Lebih clean, tidak cluttered di slice pie
   - Nama kandidat tetap ada di legend dan tooltip
2. **Guard check dengan `typeof`**: Lebih aman daripada langsung `Chart.register(ChartDataLabels)` yang bisa throw ReferenceError

### Verification Steps

#### 1. Visual Testing
```
1. Refresh dashboard admin
2. Pie chart harus tampilkan persentase (contoh: "45.2%") di setiap slice
3. Nama kandidat TIDAK boleh muncul di slice (cuma di legend & tooltip)
4. Text persentase: putih, bold, size 14px
5. Total semua persentase = 100%
```

#### 2. Technical Check
- [ ] No console errors terkait `ChartDataLabels is not defined`
- [ ] Plugin datalabels ter-load di Network tab (devtools)
- [ ] Pie chart render dengan label persentase di setiap slice

#### 3. Tooltip Check
- [ ] Hover ke slice → tooltip tetap muncul dengan format "Nama: 300 (45.2%)"
- [ ] Legend tetap tampil dengan nama kandidat di atas chart

#### 4. Edge Cases
- **Kandidat = 1**: Pie chart full circle, label "100%" centered
- **Kandidat banyak (>5)**: Label persentase tetap terlihat (lebih ringan karena cuma %)
- **Kandidat dengan votes = 0**: "0.0%" muncul di slice (acceptable)

## Implementation Steps

### Step 1: Update pie.blade.php formatter
Edit `resources/views/dashboard/components/pie.blade.php`:
- Baris 62-67: Ubah `return label + '\n' + percentage + '%';` → `return percentage + '%';`
- Hapus baris 65 (`const label = ...`) karena tidak diperlukan lagi

### Step 2: Guard check ChartDataLabels
Edit `resources/views/dashboard/components/pie.blade.php`:
- Baris 73-74: Tambah `if (typeof ChartDataLabels !== 'undefined')` sebelum `Chart.register()`

### Step 3: Tambah defer di CDN script
Edit `resources/views/dashboard/layouts/main.blade.php`:
- Baris 105: Tambah `defer` attribute

### Step 4: Update JSDoc
Edit `public/assets/js/chart-utils.js`:
- Baris 13-15: Update nilai default di komentar
- Baris 21, 26: Update contoh di komentar

## Rollback Plan
Jika persentase masih tidak muncul setelah implementasi:
1. Cek Network tab di devtools — pastikan `chartjs-plugin-datalabels.min.js` ter-load (200 OK)
2. Cek console untuk error `ChartDataLabels is not defined`
3. Jika CDN blocked, download file ke `public/assets/vendor/js/chartjs-plugin-datalabels.min.js` dan ganti src ke local asset
