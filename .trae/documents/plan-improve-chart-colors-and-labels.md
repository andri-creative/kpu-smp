# Plan: Perbaikan Warna Chart dan Tambah Persentase di Pie Chart

## Ringkasan
Memperbaiki visual chart berdasarkan feedback user:
1. **Warna lebih jelas**: Naikkan saturation & opacity, hilangkan border agar background lebih solid dan terang
2. **Tambah persentase di pie chart**: Tampilkan % langsung di setiap slice pie (data labels)
3. **Konsistensi**: Terapkan perubahan warna di pie chart dan bar chart kandidat

## Analisis Current State

### Masalah yang Diidentifikasi

#### 1. Warna Kurang Jelas (Pucat)
**File:** `public/assets/js/chart-utils.js`
- Baris 29: `saturation = 70` → masih belum cukup vibrant
- Baris 30: `lightness = 60` → terlalu terang, warna jadi pucat
- Baris 31: `opacity = 0.2` → **sangat transparan**, ini penyebab utama warna terlihat pucat
- Baris 44: `borderColors.push(...)` → border masih ada (borderWidth: 1)

**Screenshot user menunjukkan**: Warna pie dan bar sangat pucat, hampir seperti pastel wash

#### 2. Persentase Pie Hilang
**File:** `resources/views/dashboard/components/pie.blade.php`
- Baris 35-46: Config chart hanya punya `legend` dan `title` plugin
- **Tidak ada** config `datalabels` atau custom label formatter untuk tampilkan persentase
- Chart.js default pie chart tidak otomatis tampilkan persentase

#### 3. Border Masih Ada
**File:** `pie.blade.php` baris 27, `bar.blade.php` baris 28
- `borderWidth: 1` → border hitam tipis masih nampak di sekeliling warna
- User minta dihilangkan supaya background lebih solid

### File yang Terpengaruh
- `public/assets/js/chart-utils.js` → fungsi global color generator
- `resources/views/dashboard/components/pie.blade.php` → pie chart
- `resources/views/dashboard/components/bar.blade.php` → bar chart kandidat (konsistensi)

## Proposed Changes

### 1. File: `public/assets/js/chart-utils.js`
**Apa yang diubah:**
- **Naikkan saturation**: 70 → **85** (warna lebih jenuh/vibrant)
- **Turunkan lightness**: 60 → **55** (warna lebih gelap/bold, tidak pucat)
- **Naikkan opacity**: 0.2 → **0.7** (background lebih solid, hampir opaque)
- **Hilangkan border**: Set borderColor array tetap return tapi borderWidth di component diset ke 0

**Mengapa:**
- **Saturation 85%**: Menghasilkan warna yang kuat dan jelas tanpa terlalu "neon" (sweet spot 80-90%)
- **Lightness 55%**: Memberikan kontras yang baik antara warna berbeda, tidak terlalu gelap (50%) atau terlalu terang (60%+)
- **Opacity 0.7**: Hampir solid tapi masih ada sedikit transparansi untuk depth visual (0.8-0.9 terlalu flat, 0.5-0.6 masih kurang jelas)
- **Border dihilangkan**: Dengan warna solid, border tidak diperlukan lagi dan malah mengurangi area warna yang terlihat

**Bagaimana:**
```javascript
function generateDistinctColors(count, options = {}) {
    const saturation = options.saturation || 85;  // ← Dari 70 jadi 85
    const lightness = options.lightness || 55;    // ← Dari 60 jadi 55
    const opacity = options.opacity !== undefined ? options.opacity : 0.7;  // ← Dari 0.2 jadi 0.7
    
    const backgroundColors = [];
    const borderColors = [];
    
    for (let i = 0; i < count; i++) {
        const hue = (i * 360 / count) % 360;
        
        backgroundColors.push(`hsla(${hue}, ${saturation}%, ${lightness}%, ${opacity})`);
        
        // Border tetap ada untuk backward compatibility tapi borderWidth akan di-set 0
        borderColors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
    }
    
    return {
        backgroundColor: backgroundColors,
        borderColor: borderColors
    };
}
```

**Lokasi:** Baris 29-31 dan 41-44

### 2. File: `resources/views/dashboard/components/pie.blade.php`
**Apa yang diubah:**
- Set `borderWidth: 0` (dari 1) → hilangkan border
- Tambahkan plugin `datalabels` untuk tampilkan persentase di setiap slice
- Custom formatter untuk hitung dan tampilkan persentase dengan format "Nama\n%"

**Mengapa:**
- **borderWidth: 0**: User eksplisit minta hilangkan border untuk warna lebih jelas
- **Datalabels plugin**: Chart.js tidak punya built-in label di slice, perlu custom plugin atau manual config
- **Format "Nama\n%"**: User pilih opsi "Di label pie (langsung di slice)" → perlu tampilkan nama kandidat + persentase langsung di potongan pie

**Bagaimana:**
```javascript
const colors = generateDistinctColors(kandidatData.length);

const data = {
    labels: labels,
    datasets: [{
        label: 'Distribusi Suara per Kandidat',
        data: dataVotes,
        backgroundColor: colors.backgroundColor,
        borderColor: colors.borderColor,
        borderWidth: 0  // ← Dari 1 jadi 0
    }]
};

const config = {
    type: 'pie',
    data: data,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Distribusi Suara per Kandidat'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            },
            // Plugin untuk tampilkan persentase langsung di slice pie
            datalabels: {
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 14
                },
                formatter: function(value, context) {
                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    const label = context.chart.data.labels[context.dataIndex];
                    // Tampilkan nama kandidat dan persentase (line break dengan \n)
                    return label + '\n' + percentage + '%';
                }
            }
        }
    },
};
```

**CATATAN PENTING**: Chart.js **TIDAK** include plugin datalabels secara default. Perlu check apakah `chartjs-plugin-datalabels` sudah ter-load di project ini. Jika tidak, ada 2 opsi:
- **Opsi A** (Ideal): Install `chartjs-plugin-datalabels` via CDN/npm dan include di `main.blade.php`
- **Opsi B** (Fallback manual): Pakai canvas API manual untuk render text di atas slice (lebih kompleks tapi tidak butuh library tambahan)

**Lokasi:** Baris 27 (borderWidth) dan baris 35-46 (plugin config)

### 3. File: `resources/views/dashboard/components/bar.blade.php`
**Apa yang diubah:**
- Set `borderWidth: 0` untuk bar chart kandidat (konsistensi dengan pie)

**Mengapa:**
- Konsistensi visual dengan pie chart
- Warna bar kandidat juga akan lebih solid dan jelas tanpa border

**Bagaimana:**
```javascript
const colorsKandidat = generateDistinctColors(kandidatData.length);

const dataKandidat = {
    labels: labelsKandidat,
    datasets: [{
        label: 'Jumlah Suara',
        data: dataVotesKandidat,
        backgroundColor: colorsKandidat.backgroundColor,
        borderColor: colorsKandidat.borderColor,
        borderWidth: 0  // ← Dari 1 jadi 0
    }]
};
```

**Catatan**: Bar chart pemilih per kelas (baris 69-72) **TIDAK DIUBAH** karena itu single dataset kuning yang by design, bukan bagian dari dynamic color system.

**Lokasi:** Baris 28

### 4. File: `resources/views/dashboard/layouts/main.blade.php` (Conditional)
**Apa yang diubah (jika plugin datalabels belum ada):**
- Tambahkan CDN script untuk `chartjs-plugin-datalabels`

**Mengapa:**
- Chart.js core tidak include datalabels plugin
- Tanpa plugin ini, persentase di pie slice tidak akan muncul

**Bagaimana:**
```html
{{-- Chart utilities (HARUS sebelum component scripts) --}}
<script src="{{ asset('assets/js/chart-utils.js') }}"></script>

{{-- Cards --}}
<script src="{{ asset('assets/vendor/js/chart.umd.min.js') }}"></script>

{{-- Chart.js Datalabels Plugin untuk tampilkan % di pie slice --}}
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
```

**Lokasi:** Setelah baris chart-utils.js, sebelum component scripts di-load

**Alternatif jika tidak mau pakai CDN:** Download `chartjs-plugin-datalabels.min.js` dan simpan di `public/assets/vendor/js/`, lalu ganti src ke `{{ asset('assets/vendor/js/chartjs-plugin-datalabels.min.js') }}`

## Assumptions & Decisions

### Asumsi Teknis
1. **Chart.js version**: Assume menggunakan Chart.js v3+ (dari file path `chart.umd.min.js` dan syntax config)
2. **Browser support**: HSL/HSLA dengan opacity tinggi (0.7) didukung semua modern browsers
3. **Datalabels plugin**: Assume belum ter-install, perlu tambahkan via CDN

### Keputusan Desain

#### 1. Parameter Warna
| Parameter | Nilai Lama | Nilai Baru | Alasan |
|-----------|------------|------------|--------|
| Saturation | 70% | **85%** | Warna lebih jenuh, tidak pucat |
| Lightness | 60% | **55%** | Warna lebih bold, kontras lebih baik |
| Opacity | 0.2 | **0.7** | Background hampir solid, jelas terlihat |

**Mengapa tidak 100% opacity?** → Opacity 0.8-0.9 terlalu flat, kehilangan depth visual. 0.7 adalah sweet spot antara solid dan tetap punya sedikit transparansi untuk estetika.

#### 2. Format Persentase di Pie
- **Format**: `"Nama Kandidat\n45.2%"` (line break dengan \n)
- **Warna text**: Putih (`#fff`) agar kontras dengan background warna gelap
- **Font size**: 14px, bold
- **Posisi**: Default centered di setiap slice (otomatis dari plugin datalabels)

**Trade-off**: Jika slice terlalu kecil (kandidat dengan suara sangat sedikit < 5%), text mungkin terpotong atau overlap. Solusi: datalabels punya auto-hide untuk slice kecil.

#### 3. Border Dihilangkan
- `borderWidth: 0` di pie dan bar kandidat
- Border **TIDAK dihilangkan** di bar chart kelas (karena single color, border membantu definisi visual)

### Verification Requirements

#### Visual Check (Manual Testing)
1. **Warna lebih jelas**:
   - [ ] Pie chart slice terlihat vibrant, tidak pucat
   - [ ] Bar chart kandidat warna solid, bukan transparan
   - [ ] Tidak ada border hitam/putih di sekeliling warna

2. **Persentase tampil**:
   - [ ] Setiap slice pie tampilkan "Nama Kandidat" + "XX.X%"
   - [ ] Text putih, bold, size 14px
   - [ ] Persentase tepat (total semua slice = 100%)
   - [ ] Slice kecil (< 5%) auto-hide label atau tetap readable

3. **Tooltip tetap berfungsi**:
   - [ ] Hover ke slice pie → tooltip muncul dengan format "Nama: 300 (45.2%)"
   - [ ] Hover ke bar → tooltip muncul normal

#### Technical Check
- [ ] No console errors terkait datalabels plugin
- [ ] Plugin datalabels ter-load dengan benar (cek via devtools Network tab)
- [ ] Chart render di semua browser (Chrome, Firefox, Safari)
- [ ] Responsive - warna dan label tetap readable di mobile/tablet

#### Edge Cases
- **Kandidat = 1**: Pie chart full circle, persentase "100%", label centered
- **Kandidat banyak (>8)**: Slice kecil-kecil, label mungkin overlap → datalabels auto-adjust atau hide
- **Kandidat dengan votes = 0**: Slice tidak tampil, persentase 0% tidak muncul di pie

## Implementation Steps

### Step 1: Update Fungsi Global Color Generator
Edit `public/assets/js/chart-utils.js`:
- Baris 29: `saturation || 70` → `saturation || 85`
- Baris 30: `lightness || 60` → `lightness || 55`
- Baris 31: `opacity ... 0.2` → `opacity ... 0.7`

### Step 2: Tambahkan Datalabels Plugin
Edit `resources/views/dashboard/layouts/main.blade.php`:
- Tambahkan script tag CDN `chartjs-plugin-datalabels@2.2.0` setelah chart.umd.min.js

### Step 3: Update Pie Chart Config
Edit `resources/views/dashboard/components/pie.blade.php`:
- Baris 27: `borderWidth: 1` → `borderWidth: 0`
- Baris 37-46: Tambah config `tooltip.callbacks.label` dan `datalabels` plugin

### Step 4: Update Bar Chart Config
Edit `resources/views/dashboard/components/bar.blade.php`:
- Baris 28: `borderWidth: 1` → `borderWidth: 0`

### Step 5: Test Visual
- Akses dashboard admin
- Verifikasi warna, persentase, dan tidak ada border
- Test di berbagai browser

## Rollback Plan
Jika implementasi gagal atau visual tidak sesuai:
1. Revert `chart-utils.js` ke nilai lama (saturation: 70, lightness: 60, opacity: 0.2)
2. Kembalikan `borderWidth: 1` di pie dan bar
3. Hapus plugin datalabels config dari pie chart
4. Remove script tag datalabels dari main.blade.php

File-file sudah di-track Git, tinggal `git checkout -- <file>` untuk rollback.
