<?php

namespace Database\Seeders;

use App\Models\Mission;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        $misiones = [
            [1, 'Vuelta de instalación',
                'El servidor recuperado está en algún punto de la red del laboratorio y todavía no sabes qué expone.',
                'Localiza la máquina, enumera el portal de UNAB Racing y encuentra la primera pista que dejó el responsable.',
                'Reconoce la red y estudia el sitio: robots.txt, el código fuente y cualquier carpeta interna. Algo importante está codificado.',
                'Bandera Verde', 1000, 'aa80f89ff88cb2fa7e98924e79342a529c0e2ee2e3be381264fdddad4bf102fc'],
            [2, 'El casco del ingeniero',
                'Entre las fotos del monoplaza hay una imagen que el responsable usó para esconder credenciales.',
                'Extrae la información oculta en la imagen y confirma el nombre en clave del monoplaza a partir de su huella.',
                'No todo lo que ves en una foto es lo único que contiene. Y un hash sirve para verificar cuál palabra es la correcta.',
                'Casco del Ingeniero', 1000, '3ce114845af955ee62f5fd63b54712f3331d95c4c561d1a34d49f76dce624bab'],
            [3, 'Radio a boxes',
                'Con la identidad del ingeniero puedes entrar al servidor y revisar las comunicaciones capturadas.',
                'Consigue acceso remoto y analiza el tráfico guardado para localizar el material cifrado y a un segundo usuario.',
                'La contraseña nace del vocabulario del equipo más el año del Gran Premio. Dentro hay una comunicación en claro que conviene seguir.',
                'Radio del Box', 1000, '21e8d179f017c0a61116d5c884920338e73bb168fea662ee40b71a72bbf02123'],
            [4, 'La caja fuerte del garaje',
                'El paquete aerodinámico está guardado en una caja fuerte digital de doble capa.',
                'Abre el contenedor cifrado, descifra el paquete protegido y verifica quién lo firmó.',
                'Primero una capa simétrica (contenedor con contraseña), luego una asimétrica (clave privada protegida). La firma revela autoría.',
                'Caja Fuerte', 1000, '6e2422699d358d2ac30a8041eae00c9c996dfac92c7bb0d486959001c2357b0f'],
            [5, 'Bandera a cuadros',
                'El último mensaje del responsable está cifrado "a mano". Descifrarlo revela cómo tomó el control total.',
                'Rompe el criptograma con tus propias matemáticas, consigue el control del servidor y cierra la operación.',
                'Un cifrado clásico con números pequeños se puede romper. El resultado te dirá cómo llegar a lo más alto del sistema.',
                'Bandera a Cuadros', 1000, '22b1452599a67010225549b18d97e7d39a72c0c68b2081103a7c1ed0e9787492'],
        ];

        foreach ($misiones as $m) {
            Mission::updateOrCreate(['orden' => $m[0]], [
                'titulo' => $m[1], 'narrativa' => $m[2], 'objetivo' => $m[3], 'pista' => $m[4],
                'insignia' => $m[5], 'puntos' => $m[6], 'flag_hash' => $m[7],
            ]);
        }
    }
}
