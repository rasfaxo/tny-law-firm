<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $this->get(route("dashboard"))->assertRedirect(route("login"));
    }

    public function test_authenticated_users_are_redirected_to_their_role_dashboard(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route("dashboard"))
            ->assertRedirect(route("admin.dashboard"));

        $this->actingAs($this->createKlien())
            ->get(route("dashboard"))
            ->assertRedirect(route("klien.dashboard"));

        $this->actingAs($this->createStafLegal())
            ->get(route("dashboard"))
            ->assertRedirect(route("staf-legal.dashboard"));
    }

    public function test_roles_cannot_access_other_role_routes(): void
    {
        $klien = $this->createKlien();
        $admin = $this->createAdmin();
        $stafLegal = $this->createStafLegal();

        $this->actingAs($klien)
            ->get(route("admin.dashboard"))
            ->assertForbidden();

        $this->actingAs($klien)
            ->get(route("staf-legal.dashboard"))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route("klien.pra-pendaftaran.store"), [
                "id_kategori" => $this->createKategori()->id_kategori,
                "judul_perkara" => "Pengajuan tidak sah",
                "kronologi" => "Admin tidak boleh membuat pengajuan klien.",
            ])
            ->assertForbidden();

        $this->actingAs($stafLegal)
            ->get(route("admin.dashboard"))
            ->assertForbidden();
    }

    public function test_inactive_account_cannot_login_or_access_dashboard(): void
    {
        $inactiveUser = $this->createKlien(["status_akun" => "nonaktif"]);

        $this->post(route("login"), [
            "email" => $inactiveUser->email,
            "password" => "password",
        ])->assertSessionHasErrors("email");

        $this->assertGuest();

        $this->actingAs($inactiveUser)
            ->get(route("dashboard"))
            ->assertRedirect(route("login"));

        $this->assertGuest();
    }
}
