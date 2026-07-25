# Design System — IT Helpdesk

Panduan ini menjadi sumber utama untuk antarmuka aplikasi: tipografi tegas, ruang kosong luas, garis tipis, dan komponen produk berbasis HTML/CSS.

## Prinsip

1. **Jelas sebelum dekoratif** — informasi tiket dan tindakan utama harus terbaca dalam sekali lihat.
2. **Tenang dan profesional** — dominan putih, hitam, dan abu-abu dengan satu aksen sage teal.
3. **Hierarchy melalui ukuran dan ruang** — bukan melalui banyak warna atau shadow.
4. **Status tetap bermakna** — warna status hanya dipakai pada badge, indikator, dan notifikasi.
5. **Responsif secara intrinsik** — grid runtuh secara natural, tabel memiliki mode scroll/kartu, dan drawer tidak menyebabkan overflow.

## Token Warna

| Token | Nilai | Penggunaan |
|---|---:|---|
| `ink` | `#171716` | Judul, tombol utama, sidebar |
| `paper` | `#FFFFFF` | Card, panel, input |
| `canvas` | `#F6F6F2` | Background aplikasi |
| `muted` | `#6D6D67` | Teks sekunder |
| `line` | `#DEDED8` | Border dan divider |
| `accent` | `#17715E` | Link aktif, fokus, indikator utama |
| `accent-dark` | `#115646` | Hover pada elemen aksen |
| `accent-soft` | `#DDF2EB` | Highlight lembut dan CTA alternatif |
| `accent-pale` | `#F0F8F5` | Background informasi |

Warna status dipakai terbatas:

- Baru: biru lembut.
- Ditugaskan: ungu lembut.
- Diproses: amber lembut.
- Menunggu konfirmasi: cyan lembut.
- Selesai/baik/tersedia: hijau lembut.
- Kritis/rusak/error: merah lembut.
- Dibatalkan/nonaktif: abu-abu.

## Tipografi

- Font: `Inter`, `ui-sans-serif`, `system-ui`, tanpa font remote wajib.
- Display landing: 56–76 px desktop, 42–52 px mobile; weight 700; line-height 0.98–1.05.
- Judul halaman: 30–40 px; weight 700; tracking rapat.
- Heading bagian: 22–30 px; weight 650–700.
- Body: 14–16 px; line-height 1.6.
- Label/meta: 11–13 px; weight 600; tracking sedikit lebar untuk uppercase.

## Spacing dan Layout

- Unit dasar: 4 px.
- Spacing komponen: 12, 16, 20, 24, 32 px.
- Spacing antarseksi: 48–96 px.
- Lebar konten aplikasi: fleksibel; padding 20 px mobile, 32–40 px desktop.
- Lebar landing: maksimal 1280 px.
- Sidebar: 264 px desktop; drawer penuh hingga 304 px pada mobile.

## Bentuk Komponen

- Radius kecil: 8 px untuk input, button, badge.
- Radius panel: 12 px.
- Border: 1 px `line`.
- Shadow: tidak digunakan pada card biasa; hanya untuk drawer/modal/dropdown.
- Button: tinggi minimum 42 px, label 13–14 px, weight 650.
- Input: tinggi minimum 44 px, background putih, focus ring aksen 2 px.
- Card: border tipis, background putih, padding 20–24 px.

## Komponen Global

### Button

- Primary: background `ink`, teks putih.
- Accent: background `accent`, teks putih untuk aksi kontekstual.
- Secondary: putih, border `line`, teks `ink`.
- Danger: putih/merah lembut, teks merah; digunakan hemat.
- Semua memiliki focus-visible ring dan disabled/loading state.

### Card dan Panel

- `card`: kontainer informasi umum tanpa shadow.
- `panel`: section berjudul dengan header dan body terpisah.
- `stat-card`: label kecil, nilai besar, dan indikator minimal.

### Badge

- Tinggi ringkas, radius penuh, dot indikator, teks title case.
- Warna mengikuti status/prioritas; tidak digunakan sebagai dekorasi umum.

### Alert

- Border dan background lembut; ikon, judul opsional, isi, tombol tutup opsional.
- Success, error, warning, info menggunakan semantik yang konsisten.

### Modal

- Backdrop gelap transparan, panel putih maksimal 560 px, radius 12 px.
- Fokus keyboard dan tombol batal/konfirmasi jelas.

### Empty State

- Ikon garis orisinal dalam kotak 44 px, judul, deskripsi singkat, CTA opsional.
- Tidak memakai ilustrasi atau aset eksternal.

### Loading State

- Spinner CSS 16–20 px dan skeleton berwarna abu lembut.
- Form menampilkan state pada submit tanpa mengubah request/backend.

### Tabel

- Header putih/abu sangat muda, uppercase 11 px, divider horizontal.
- Tidak memakai zebra stripe; row hover sangat lembut.
- Desktop horizontal bila kolom banyak; mobile menjaga aksi dan identitas utama tetap terbaca.

### Sidebar dan Navbar

- Sidebar putih dengan border kanan; logo hitam + aksen sage.
- Item aktif memakai `accent-soft`, teks `accent`, dan indikator vertikal.
- Navbar putih/transparan dengan breadcrumb/judul dan menu akun.
- Mobile memakai drawer dengan overlay dan tombol close yang memiliki label aksesibel.

## Landing Page

- Navbar sederhana dengan wordmark “IT Helpdesk”.
- Hero big type dan preview UI buatan sendiri.
- Tiga feature story bernomor `01`, `02`, `03` dalam pola bergantian.
- Workflow enam langkah, feature grid, CTA, dan footer.
- Tidak memakai logo, copy, screenshot, gambar, atau ilustrasi dari referensi.

## Aksesibilitas

- Kontras teks minimal WCAG AA.
- Seluruh form memiliki label yang terhubung ke input.
- Focus state selalu terlihat.
- Ikon dekoratif memakai `aria-hidden`; tombol ikon memiliki `aria-label`.
- Tabel memakai `thead`, `th`, dan caption visual bila diperlukan.
- Animasi menghormati `prefers-reduced-motion`.

## Aturan Implementasi

- Prioritaskan komponen Blade dan kelas `@layer components` daripada duplikasi pola.
- Jangan menaruh business logic baru dalam view.
- Jangan mengubah nama route, field, policy, atau workflow tiket untuk kebutuhan visual.
- Semua copy antarmuka menggunakan Bahasa Indonesia.
