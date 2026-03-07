<?php

namespace Modules\SocialAccount\Installations;

use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Artisan;

class PostInstallation
{
  public function handle(string $moduleName) {
    try {
      $modules = array_merge(["coreui", "users"], [$moduleName]);

      foreach ($modules as $modulename) {
        $module = Module::find($modulename);
        $module->enable();
      }

      Artisan::call("migrate", ["--force" => true]);
    } catch (\Exception $e) {
      logger()->error(
        "Failed to run post installation of social account module: " .
        $e->getMessage()
      );

      throw $e;
    }
  }
}