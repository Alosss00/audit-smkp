<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityConfigurationTest extends TestCase
{
    /**
     * .env file is strictly inaccessible via HTTP requests.
     */
    public function test_env_file_cannot_be_accessed_via_http(): void
    {
        $response = $this->get('/.env');

        $this->assertNotSame(200, $response->getStatusCode());
        $response->assertStatus(404);
        $this->assertStringNotContainsString('DB_PASSWORD', $response->getContent());
        $this->assertStringNotContainsString('APP_KEY', $response->getContent());
    }

    /**
     * Dev tools scripts and documentation are inaccessible via HTTP requests.
     */
    public function test_dev_tools_scripts_cannot_be_accessed_via_http(): void
    {
        $response1 = $this->get('/dev-tools/inspect_excel_p1.php');
        $this->assertNotSame(200, $response1->getStatusCode());
        $response1->assertStatus(404);

        $response2 = $this->get('/dev-tools/README.md');
        $this->assertNotSame(200, $response2->getStatusCode());
        $response2->assertStatus(404);

        $response3 = $this->get('/dev-tools/_smkp_relasi_feature/verify.php');
        $this->assertNotSame(200, $response3->getStatusCode());
        $response3->assertStatus(404);
    }

    /**
     * Critical dependency files (.git, composer.json, composer.lock) cannot be accessed via HTTP.
     */
    public function test_composer_and_git_files_cannot_be_accessed_via_http(): void
    {
        $responseGit = $this->get('/.git/config');
        $responseGit->assertStatus(404);

        $responseComposer = $this->get('/composer.json');
        $responseComposer->assertStatus(404);

        $responseLock = $this->get('/composer.lock');
        $responseLock->assertStatus(404);
    }

    /**
     * Direct HTTP access to private storage directory is blocked / not found.
     */
    public function test_direct_storage_app_access_cannot_be_accessed_via_http(): void
    {
        $response = $this->get('/storage/app/');
        $response->assertStatus(404);
    }

    /**
     * Verify root .htaccess contains strict deny rules for dev-tools, .env, and composer.
     */
    public function test_root_htaccess_contains_block_rules(): void
    {
        $path = base_path('.htaccess');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('dev-tools/', $content);
        $this->assertStringContainsString('\.env', $content);
        $this->assertStringContainsString('composer\.', $content);
    }

    /**
     * Verify dev-tools/.htaccess contains complete denial of HTTP access.
     */
    public function test_dev_tools_htaccess_contains_deny_all(): void
    {
        $path = base_path('dev-tools/.htaccess');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('Require all denied', $content);
        $this->assertStringContainsString('Deny from all', $content);
    }

    /**
     * Verify public/.htaccess enforces script execution lockdown to index.php only.
     */
    public function test_public_htaccess_contains_index_php_only_guard(): void
    {
        $path = public_path('.htaccess');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('<FilesMatch "\.php$">', $content);
        $this->assertStringContainsString('<Files "index.php">', $content);
        $this->assertStringContainsString('storage/(lampiran|bukti_pica)/', $content);
    }
}
