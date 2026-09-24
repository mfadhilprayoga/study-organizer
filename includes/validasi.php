<?php

function validasiJudulCatatan($title) {
    $title = trim($title);
    if ($title === '') {
        return "Judul tidak boleh kosong.";
    }
    if (strlen($title) > 100) {
        return "Judul maksimal 100 karakter.";
    }
    return null;
}

function validasiIsiCatatan($content) {
    $content = trim($content);
    if (strlen($content) < 10) {
        return "Isi catatan minimal 10 karakter.";
    }
    return null;
}

function validasiNamaTugas($title) {
    $title = trim($title);
    if ($title === '') {
        return "Nama tugas tidak boleh kosong.";
    }
    if (strlen($title) > 150) {
        return "Nama tugas maksimal 150 karakter.";
    }
    return null;
}

function validasiDeadlineTugas($task_date, $deadline) {
    if ($deadline < $task_date) {
        return "Deadline tidak boleh sebelum tanggal dibuat.";
    }
    return null;
}
