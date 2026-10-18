<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/validasi.php';

class ValidasiTest extends TestCase
{
    public function testJudulKosongDitolak()
    {
        $hasil = validasiJudulCatatan('');
        $this->assertEquals("Judul tidak boleh kosong.", $hasil);
    }

    public function testJudulValidDiterima()
    {
        $hasil = validasiJudulCatatan('Belajar PHPUnit');
        $this->assertNull($hasil);
    }

    public function testJudulTerlaluPanjangDitolak()
    {
        $judulPanjang = str_repeat('a', 101);
        $hasil = validasiJudulCatatan($judulPanjang);
        $this->assertEquals("Judul maksimal 100 karakter.", $hasil);
    }

    public function testIsiCatatanTerlaluPendekDitolak()
    {
        $hasil = validasiIsiCatatan('pendek');
        $this->assertEquals("Isi catatan minimal 10 karakter.", $hasil);
    }

    public function testIsiCatatanValidDiterima()
    {
        $hasil = validasiIsiCatatan('Ini adalah isi catatan yang cukup panjang.');
        $this->assertNull($hasil);
    }

    public function testDeadlineSebelumTanggalDibuatDitolak()
    {
        $hasil = validasiDeadlineTugas('2026-09-20', '2026-09-15');
        $this->assertEquals("Deadline tidak boleh sebelum tanggal dibuat.", $hasil);
    }

    public function testDeadlineValidDiterima()
    {
        $hasil = validasiDeadlineTugas('2026-09-20', '2026-09-25');
        $this->assertNull($hasil);
    }
   public function testNamaTugasKosongDitolak()
    {
        $hasil = validasiNamaTugas('');
        $this->assertEquals("Nama tugas tidak boleh kosong.", $hasil);
    }

   public function testNamaTugasValidDiterima()
    {
        $hasil = validasiNamaTugas('Mengerjakan laporan');
        $this->assertNull($hasil);
    }

   public function testEmailTidakValidDitolak()
    {
        $hasil = validasiEmail('email-tidak-valid');
        $this->assertEquals("Format email tidak valid.", $hasil);
    }

   public function testEmailValidDiterima()
    {
        $hasil = validasiEmail('fadhil@example.com');
        $this->assertNull($hasil);
    }

   public function testNamaLengkapKosongDitolak()
    {
        $hasil = validasiNamaLengkap('');
        $this->assertEquals("Nama tidak boleh kosong.", $hasil);
    }

   public function testNamaLengkapValidDiterima()
    {
        $hasil = validasiNamaLengkap('Fadhil Prayoga');
        $this->assertNull($hasil);
    }

}

