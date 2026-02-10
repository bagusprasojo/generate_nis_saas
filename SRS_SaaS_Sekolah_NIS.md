# 📘 Software Requirement Specification (SRS)

## SaaS Manajemen Sekolah (Sekolah & Siswa) dengan NIS Pattern Engine

**Tech Stack:** Laravel 12 + MySQL

Fokus sistem ini hanya: - Input data **Sekolah** - Input data
**Siswa** - **Generate NIS otomatis** berdasarkan **pola berbeda tiap
sekolah**

------------------------------------------------------------------------

## 1. Pendahuluan

### 1.1 Tujuan

Dokumen ini menjelaskan spesifikasi kebutuhan sistem manajemen sekolah
sederhana berbasis SaaS yang mendukung: - Multi sekolah - Manajemen data
siswa - Generate NIS dinamis berdasarkan pola masing-masing sekolah

### 1.2 Ruang Lingkup

Sistem hanya memiliki 3 modul utama: 1. Manajemen Sekolah 2. Manajemen
Pola NIS (NIS Pattern Engine) 3. Manajemen Siswa

Tidak ada modul akademik, keuangan, atau lainnya.

------------------------------------------------------------------------

## 2. Gambaran Umum Sistem

Sistem bersifat **multi-tenant (SaaS)**: - Banyak sekolah dalam 1
aplikasi - Setiap sekolah memiliki aturan NIS sendiri - NIS **tidak
di-hardcode**, tetapi dihasilkan dari template/pattern

------------------------------------------------------------------------

## 3. Definisi Konsep Kunci

### 3.1 NIS Bukan Field Biasa

NIS adalah hasil dari:

    Pattern + Sequence + Data Sekolah + Data Siswa

### 3.2 Contoh Pattern

  Pattern                        Hasil
  ------------------------------ --------------
  `{YEAR}{SEQ:4}`                20260001
  `SMP01/{YEAR_SHORT}/{SEQ:3}`   SMP01/26/001
  `{SCHOOL_CODE}-{SEQ:5}`        SCH01-00001

------------------------------------------------------------------------

## 4. Kebutuhan Fungsional

### 4.1 Modul Sekolah

#### Fitur

-   Tambah sekolah
-   Edit sekolah
-   Hapus sekolah
-   Set kode sekolah (`school_code`)

#### Struktur Data Sekolah

  Field        Tipe
  ------------ -----------
  id           bigint
  name         varchar
  code         varchar
  address      text
  created_at   timestamp

------------------------------------------------------------------------

### 4.2 Modul NIS Pattern (Inti Sistem)

Setiap sekolah **WAJIB** memiliki 1 pola NIS.

#### Fitur

-   Set pola NIS
-   Pilih aturan reset sequence
-   Preview hasil NIS
-   Simpan last sequence

#### Placeholder yang Didukung

  Placeholder       Arti
  ----------------- -----------------------------------
  `{YEAR}`          Tahun sekarang (2026)
  `{YEAR_SHORT}`    26
  `{SCHOOL_CODE}`   Kode sekolah
  `{INTAKE_YEAR}`   Tahun masuk siswa
  `{SEQ:n}`         Nomor urut dengan padding n digit

#### Aturan Reset

  Rule     Arti
  -------- ---------------------------
  yearly   Reset tiap tahun
  intake   Reset tiap angkatan siswa
  never    Tidak pernah reset

------------------------------------------------------------------------

### 4.3 Modul Siswa

#### Fitur

-   Tambah siswa
-   NIS otomatis di-generate
-   List siswa per sekolah

#### Struktur Data Siswa

  Field         Tipe
  ------------- ------------------
  id            bigint
  school_id     bigint
  name          varchar
  intake_year   year
  nis           varchar (unique)
  created_at    timestamp

------------------------------------------------------------------------

## 5. Proses Generate NIS (Core Logic)

### 5.1 Flow

    Input siswa baru
        ↓
    Ambil nis_pattern milik sekolah
        ↓
    Tentukan sequence berdasarkan reset_rule
        ↓
    Generate NIS dari pattern
        ↓
    Simpan siswa + update sequence

------------------------------------------------------------------------

## 6. Kebutuhan Non-Fungsional

  Aspek          Kebutuhan
  -------------- ------------------------------
  Framework      Laravel 12
  DB             MySQL
  Styling        Bootsrap
  Javascript     Alpine.js
  Keamanan       Validasi input, unique NIS
  Performa       Aman untuk concurrent insert
  Multi-tenant   Berdasarkan `school_id`

------------------------------------------------------------------------

## ✅ Ringkasan Inti SRS

> **NIS dihasilkan oleh Pattern Engine berbasis database, bukan dari
> kode program.**
