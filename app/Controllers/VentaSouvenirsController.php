<?php

namespace App\Controllers;

use App\Models\IngresosModel;
use App\Models\VentaEntradasModel;
use App\Models\VentaSouvenirsModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class VentaSouvenirsController extends BaseController
{
    private const COSTO_DESPACHO = 10000;
    private const PRECIO_ENTRADA = 5000;

    public function __construct()
    {
        helper('form');
    }

    public function cargaArticulos()
    {
        $db = db_connect();
        $results = $db->table('souvenirs s')
            ->select('s.producto, s.talla, s.precio, s.genero, s.detalle, s.foto, s.stock, MIN(s.id) AS id')
            ->groupBy(['s.producto', 's.talla', 's.precio', 's.genero', 's.detalle', 's.foto', 's.stock'])
            ->get()->getResultArray();

        return view('templates/header')
            . view('home/venta_souvenirs', ['title' => 'Tienda oficial', 'results' => $results])
            . view('templates/footer');
    }

    public function detalleProducto($id = null)
    {
        if (! ctype_digit((string) $id)) {
            return redirect()->to(site_url('TiendaOficial'));
        }

        $db = db_connect();
        $producto = $db->table('souvenirs')->where('id', (int) $id)->get()->getRow();
        if ($producto === null) {
            return redirect()->to(site_url('TiendaOficial'))->with('error', 'Producto no encontrado.');
        }

        $tallas = $db->table('souvenirs')->where('producto', $producto->producto)->get()->getResult();
        return view('templates/header')
            . view('home/detalle_producto', [
                'title' => 'Detalle del producto',
                'producto' => [
                    'id' => $producto->id,
                    'producto' => $producto->producto,
                    'precio' => $producto->precio,
                    'genero' => $producto->genero,
                    'detalle' => $producto->detalle,
                    'foto' => $producto->foto,
                    'tallas' => $tallas,
                ],
            ])
            . view('templates/footer');
    }

    public function agregarProducto()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $id = $this->request->getPost('id');
        if (! ctype_digit((string) $id)) {
            return redirect()->to(site_url('TiendaOficial'))->with('error', 'Producto inválido.');
        }

        $producto = db_connect()->table('souvenirs')->where('id', (int) $id)->get()->getRow();
        if ($producto === null || (int) $producto->stock < 1) {
            return redirect()->back()->with('error', 'El producto o la talla no están disponibles.');
        }

        $carro = session('carro') ?: [];
        $key = $this->cartKey((int) $producto->id, (string) $producto->talla);
        $cantidad = isset($carro[$key]) ? (int) $carro[$key] : 0;
        if ($cantidad >= (int) $producto->stock || $cantidad >= 99) {
            return redirect()->back()->with('error', 'No hay stock suficiente para agregar otra unidad.');
        }
        $carro[$key] = $cantidad + 1;
        session()->set('carro', $carro);

        return redirect()->to(site_url('CarroCompras'))->with('success', 'Producto agregado al carro.');
    }

    public function mostrarCarro()
    {
        [$productos, $total] = $this->getCartLines();
        session()->set('productos', $productos);
        return view('templates/header')
            . view('home/carro_compras', ['title' => 'Carro de compras', 'productos' => $productos, 'total' => $total])
            . view('templates/footer');
    }

    public function vaciarCarro()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }
        session()->remove(['carro', 'productos']);
        return redirect()->to(site_url('TiendaOficial'));
    }

    public function eliminarProducto()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }
        $key = (string) $this->request->getPost('item');
        $carro = session('carro') ?: [];
        if (isset($carro[$key])) {
            if ((int) $carro[$key] > 1) {
                $carro[$key] = (int) $carro[$key] - 1;
            } else {
                unset($carro[$key]);
            }
            session()->set('carro', $carro);
        }
        return redirect()->to(site_url('CarroCompras'));
    }

    public function checkout()
    {
        [$productos, $total, $error] = $this->getCartLines(true);
        if ($error !== null || $productos === []) {
            return redirect()->to(site_url('CarroCompras'))->with('error', $error ?? 'El carro está vacío.');
        }
        return view('templates/header')
            . view('home/checkout', ['title' => 'Checkout', 'productos' => $productos, 'total' => $total])
            . view('templates/footer');
    }

    public function confirmarCompra()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $data = [
            'nombre' => trim((string) $this->request->getPost('nombre')),
            'telefono' => trim((string) $this->request->getPost('telefono')),
            'entrega' => (string) $this->request->getPost('entrega'),
            'direccion' => trim((string) $this->request->getPost('direccion')),
        ];
        $rules = [
            'nombre' => 'required|max_length[100]',
            'telefono' => 'required|regex_match[/^[0-9]{8,9}$/]',
            'entrega' => 'required|in_list[retiro,despacho]',
        ];
        if ($data['entrega'] === 'despacho') {
            $rules['direccion'] = 'required|max_length[100]';
        }
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('error', 'Revisa el nombre, teléfono y los datos de entrega.');
        }

        [$productos, $subtotal, $error] = $this->getCartLines(true);
        if ($error !== null || $productos === []) {
            return redirect()->to(site_url('CarroCompras'))->with('error', $error ?? 'El carro está vacío.');
        }

        $total = $subtotal + ($data['entrega'] === 'despacho' ? self::COSTO_DESPACHO : 0);
        $db = db_connect();
        $db->transBegin();
        foreach ($productos as $producto) {
            $db->query('UPDATE souvenirs SET stock = stock - ? WHERE id = ? AND stock >= ?', [
                $producto['cantidad'], $producto['id'], $producto['cantidad'],
            ]);
            if ($db->affectedRows() !== 1) {
                $db->transRollback();
                return redirect()->to(site_url('CarroCompras'))->with('error', 'El stock cambió mientras completabas la compra. Revisa el carro.');
            }
        }

        $ventas = new VentaSouvenirsModel();
        $detalle = [];
        foreach ($productos as $producto) {
            $detalle[] = sprintf('%s%s | $%s x %d = $%s',
                $producto['nombre'], $producto['talla'] !== '' ? ' (' . $producto['talla'] . ')' : '',
                number_format($producto['precio'], 0, ',', '.'), $producto['cantidad'],
                number_format($producto['precio'] * $producto['cantidad'], 0, ',', '.')
            );
        }
        if ($data['entrega'] === 'despacho') {
            $detalle[] = 'Despacho: $' . number_format(self::COSTO_DESPACHO, 0, ',', '.');
        }
        $ventas->insert([
            'monto_compra' => $total,
            'articulos_vendidos' => implode("\n", $detalle),
            'nombre_comprador' => $data['nombre'],
            'telefono_comprador' => (int) preg_replace('/\D+/', '', $data['telefono']),
            'direccion_envio' => $data['entrega'] === 'despacho' ? $data['direccion'] : '',
            'fecha_compra' => date('Y-m-d H:i:s'),
            'metodo' => $data['entrega'],
        ]);
        $ventaId = $ventas->insertID();
        $ingresos = new IngresosModel();
        $ingresos->insert([
            'concepto' => 'venta_souvenirs',
            'monto' => $total,
            'fecha' => date('Y-m-d'),
            'detalle' => 'Venta de souvenirs con ' . $data['entrega'],
        ]);

        if ($db->transStatus() === false || ! $ventaId) {
            $db->transRollback();
            return redirect()->to(site_url('CarroCompras'))->with('error', 'No se pudo registrar la compra. Inténtalo de nuevo.');
        }
        $db->transCommit();

        $html = view('pdf/compra', [
            'idCompra' => $ventaId,
            'nombre' => $data['nombre'],
            'telefono' => $data['telefono'],
            'entrega' => $data['entrega'],
            'direccion' => $data['direccion'],
            'productos' => $productos,
            'total' => $total,
        ]);
        $options = new Options();
        $options->setIsRemoteEnabled(false);
        $options->setIsPhpEnabled(false);
        $options->setChroot(PUBLICPATH);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        $filename = 'boleta_compra_' . date('YmdHis') . '.pdf';
        session()->remove(['carro', 'productos']);

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function loadVentaEntradas()
    {
        $partidos = db_connect()->table('partidos p')
            ->select('p.id, p.fecha, e.nombre AS nombre_equipo_local, e2.nombre AS nombre_equipo_visita, c.nombre AS campeonato, c2.nombre AS nombre_cancha, c2.ubicacion')
            ->join('equipos e', 'e.id = p.equipo_local_fk', 'left')
            ->join('equipos e2', 'e2.id = p.equipo_visita_fk', 'left')
            ->join('campeonatos c', 'c.id = p.campeonato_id_fk', 'left')
            ->join('cancha c2', 'c2.id = p.ubicacion_fk', 'left')
            ->where('p.fecha >=', date('Y-m-d'))
            ->orderBy('p.fecha')->get()->getResultArray();
        return view('templates/header')
            . view('home/compra_entradas', ['title' => 'Venta de entradas', 'results' => $partidos])
            . view('templates/footer');
    }

    public function datosComprador($partidoId = null)
    {
        if (! ctype_digit((string) $partidoId)) {
            return redirect()->to(site_url('VentaEntradas'));
        }
        $partido = db_connect()->table('partidos')->where('id', (int) $partidoId)->where('fecha >=', date('Y-m-d'))->get()->getRow();
        if ($partido === null) {
            return redirect()->to(site_url('VentaEntradas'))->with('error', 'Partido no disponible para venta.');
        }
        return view('templates/header')
            . view('home/datos_comprador_entrada', ['title' => 'Datos del comprador', 'id' => (int) $partidoId])
            . view('templates/footer');
    }

    public function confirmarCompraEntrada()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }
        $data = [
            'nombre_comprador' => trim((string) $this->request->getPost('nombre_comprador')),
            'rut_comprador' => strtoupper(trim((string) $this->request->getPost('rut_comprador'))),
            'partidoId' => (string) $this->request->getPost('partidoId'),
        ];
        if (! $this->validateData($data, [
            'nombre_comprador' => 'required|max_length[100]',
            'rut_comprador' => 'required|max_length[12]|regex_match[/^[0-9.]+-[0-9K]$/]',
            'partidoId' => 'required|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Los datos del comprador no son válidos.');
        }

        $db = db_connect();
        $partido = $db->table('partidos p')
            ->select('p.id, p.fecha, e.nombre AS nombre_equipo_local, e2.nombre AS nombre_equipo_visita, c.nombre AS campeonato, c2.nombre AS nombre_cancha')
            ->join('equipos e', 'e.id = p.equipo_local_fk', 'left')
            ->join('equipos e2', 'e2.id = p.equipo_visita_fk', 'left')
            ->join('campeonatos c', 'c.id = p.campeonato_id_fk', 'left')
            ->join('cancha c2', 'c2.id = p.ubicacion_fk', 'left')
            ->where('p.id', (int) $data['partidoId'])->where('p.fecha >=', date('Y-m-d'))
            ->get()->getRowArray();
        if ($partido === null) {
            return redirect()->to(site_url('VentaEntradas'))->with('error', 'Partido no disponible para venta.');
        }

        $db->transBegin();
        $ventas = new VentaEntradasModel();
        $ventas->insert([
            'monto' => self::PRECIO_ENTRADA,
            'nombre_comprador' => $data['nombre_comprador'],
            'rut_comprador' => $data['rut_comprador'],
            'id_partido_fk' => (int) $partido['id'],
        ]);
        $ventaId = $ventas->insertID();
        (new IngresosModel())->insert([
            'concepto' => 'venta_entradas',
            'monto' => self::PRECIO_ENTRADA,
            'fecha' => date('Y-m-d'),
            'detalle' => 'Venta de entradas',
        ]);
        if ($db->transStatus() === false || ! $ventaId) {
            $db->transRollback();
            return redirect()->to(site_url('VentaEntradas'))->with('error', 'No se pudo registrar la compra de entrada.');
        }
        $db->transCommit();

        $html = view('pdf/compra_entradas', [
            'id_venta' => $ventaId,
            'nombre_comprador' => $data['nombre_comprador'],
            'rut_comprador' => $data['rut_comprador'],
            'nombre_equipo_local' => $partido['nombre_equipo_local'],
            'nombre_equipo_visita' => $partido['nombre_equipo_visita'],
            'campeonato' => $partido['campeonato'],
            'nombre_cancha' => $partido['nombre_cancha'],
            'monto' => self::PRECIO_ENTRADA,
        ]);
        $options = new Options();
        $options->setIsRemoteEnabled(false);
        $options->setIsPhpEnabled(false);
        $options->setChroot(PUBLICPATH);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="boleta_entrada_' . date('YmdHis') . '.pdf"')
            ->setBody($dompdf->output());
    }

    /** @return array{0: array, 1: int, 2: ?string} */
    private function getCartLines(bool $checkStock = false): array
    {
        $carro = session('carro') ?: [];
        $productos = [];
        $total = 0;
        $builder = db_connect()->table('souvenirs');
        foreach ($carro as $key => $cantidad) {
            $parts = explode('-', (string) $key, 2);
            if (count($parts) !== 2 || ! ctype_digit($parts[0]) || ! ctype_digit((string) $cantidad) || (int) $cantidad < 1 || (int) $cantidad > 99) {
                return [[], 0, 'El carro contiene datos inválidos; vacíalo e intenta nuevamente.'];
            }
            $producto = $builder->where('id', (int) $parts[0])->get()->getRow();
            $builder->resetQuery();
            if ($producto === null || (string) $producto->talla !== $parts[1]) {
                return [[], 0, 'Uno de los productos ya no está disponible.'];
            }
            if ($checkStock && (int) $producto->stock < (int) $cantidad) {
                return [[], 0, 'No queda stock suficiente de ' . $producto->producto . '.'];
            }
            $line = [
                'id' => (int) $producto->id,
                'nombre' => (string) $producto->producto,
                'foto' => $producto->foto,
                'precio' => (int) $producto->precio,
                'cantidad' => (int) $cantidad,
                'talla' => (string) $producto->talla,
            ];
            $productos[] = $line;
            $total += $line['precio'] * $line['cantidad'];
        }
        return [$productos, $total, null];
    }

    private function cartKey(int $id, string $talla): string
    {
        return $id . '-' . $talla;
    }
}