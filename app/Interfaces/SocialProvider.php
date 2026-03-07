<?php
namespace Modules\SocialAccount\Interfaces;

interface SocialProvider
{
  /**
  * Nama unik provider (misal: 'google')
  */
  public function getName(): string;

  /**
  * Label untuk ditampilkan (misal: 'Google')
  */
  public function getLabel(): string;

  /**
  * Class icon Bootstrap Icons (misal: 'bi bi-google')
  */
  public function getIcon(): string;

  /**
  * URL untuk memulai proses login (route name atau URL)
  */
  public function getLoginUrl(): string;

  /**
  * Handle callback dari provider (menerima data dari Socialite)
  * Harus membuat record di tabel provider (misal GoogleProvider) dan mengembalikan
  * array dengan data untuk disimpan di social_accounts:
  * [
  *   'providerable_id' => $record->id,
  *   'providerable_type' => get_class($record),
  *   'provider_data' => [...] // data tambahan
  * ]
  */
  public function handleCallback($socialUser): array;
}