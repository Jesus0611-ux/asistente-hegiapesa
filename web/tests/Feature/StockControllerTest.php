<?php
// Historia: H-01

namespace Tests\Feature;

use App\Models\Producto;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    public function test_existing_product_returns_real_stock_data(): void
    {
        $this->assertUsesExistingMySqlCatalog();

        $producto = Producto::where('sku', 'HEG-001')->firstOrFail();

        $this->assertSame('Cemento Portland', $producto->nombre_comercial);
        $this->assertSame(120, (int) $producto->cantidad_disponible);

        $this->get('/stock?sku=HEG-001')
            ->assertOk()
            ->assertSee('HEG-001')
            ->assertSee('Cemento Portland')
            ->assertSee('120');
    }

    public function test_nonexistent_sku_returns_not_found(): void
    {
        $this->assertUsesExistingMySqlCatalog();
        $this->assertFalse(Producto::where('sku', 'INEXISTENTE')->exists());

        $this->get('/stock?sku=INEXISTENTE')
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
