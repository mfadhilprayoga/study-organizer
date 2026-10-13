<?php
date_default_timezone_set('Asia/Jakarta');

function formatTanggalIndonesia(string $value): string
{
    $date = new DateTimeImmutable($value);
    $months = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    return $date->format('d') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
}

function dateTimeDariTimestamp(int $timestamp): DateTimeImmutable
{
    return (new DateTimeImmutable('@' . $timestamp))
        ->setTimezone(new DateTimeZone(date_default_timezone_get()));
}
