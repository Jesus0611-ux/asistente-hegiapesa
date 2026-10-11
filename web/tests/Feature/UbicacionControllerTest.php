<?php
// Historia: H-02

namespace Tests\Feature;

use App\Models\Producto;
use Tests\TestCase;

class UbicacionControllerTest extends TestCase
{
    public function test_existing_product_returns_real_location_data(): void
    {
        $this->assertUsesExistingMySqlCatalog();

        $producto = Producto::with('ubicacion')->where('sku', 'HEG-001')->firstOrFail();

        $this->assertSame('Cemento Portland', $producto->nombre_comercial);
        $this->assertNotNull($producto->ubicacion);
        $this->assertSame(1, (int) $producto->ubicacion->numero_pasillo);
        $this->assertSame('A-01', $producto->ubicacion->codigo_estante);

        $this->get('/ubicacion?sku=HEG-001')
            ->assertOk()
            ->assertSee('HEG-001')
            ->assertSee('Cemento Portland')
            ->assertSee('Pasillo 1, Estante A-01');
    }

    public function test_nonexistent_sku_returns_not_found(): void
    {
        $this->assertUsesExistingMySqlCatalog();
        $this->assertFalse(Producto::where('sku', 'INEXISTENTE')->exists());

        $this->get('/ubicacion?sku=INEXISTENTE')
            ->assertNotFound();
    }

    private function assertUsesExistingMySqlCatalog(): void
    {
        $this->assertSame('mysql', config('database.default'));
        $this->assertSame('mysql', config('database.connections.mysql.driver'));
        $this->assertSame('hegiastock', config('database.connections.mysql.database'));
        $this->assertSame('3307', (string) config('database.connections.mysql.port'));
    }
}
