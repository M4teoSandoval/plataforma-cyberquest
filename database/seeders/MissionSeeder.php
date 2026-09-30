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
                'Lunes, 09:00. El servidor del laboratorio de aerodinámica fue aislado de la red de la fábrica, pero nadie sabe todavía qué servicios expone ni qué dejó el responsable en él. Como en la vuelta de instalación, primero hay que reconocer la pista.',
                'Localiza la máquina, enumera el portal de UNAB Racing y encuentra la primera pista que dejó el responsable.',
                'Reconoce la red y estudia el sitio: robots.txt, el código fuente y cualquier carpeta interna. Algo importante está codificado.',
                'Bandera Verde', 1000, '528248762895a601f545ef0f0ef3bc20b3dc44376cd5421ec4b7a202185d4749'],
            [2, 'El casco del ingeniero',
                'Las fotos del monoplaza del portal parecen material de prensa, pero una de ellas guarda más de lo que muestra: el responsable la usó para esconder las credenciales de un ingeniero del equipo.',
                'Extrae la información oculta en la imagen y confirma el nombre en clave del monoplaza a partir de su huella.',
                'No todo lo que ves en una foto es lo único que contiene. Y un hash sirve para verificar cuál palabra es la correcta.',
                'Casco del Ingeniero', 1000, '5d2381671748943028ae1397c082112ccce8052caf1f28e45bd04ce74b26df1e'],
            [3, 'Radio a boxes',
                'Con la identidad del ingeniero puedes entrar al servidor. Allí quedaron capturas de tráfico de las comunicaciones internas del equipo, y alguien habló más de la cuenta, como una radio a boxes sin cifrar.',
                'Consigue acceso remoto y analiza el tráfico guardado para localizar el material cifrado y a un segundo usuario.',
                'La contraseña nace del vocabulario del equipo más el año del Gran Premio. Dentro hay una comunicación en claro que conviene seguir.',
                'Radio del Box', 1000, '8186e05e9e3cca3b402852c3d70bb53f44ba863441c8eab8594f01f79ffa0c26'],
            [4, 'La caja fuerte del garaje',
                'El Proyecto Rascasse está guardado en una caja fuerte digital de doble capa. Si logras abrirla y verificar quién firmó el archivo, tendrás la prueba de que el diseño salió de UNAB Racing.',
                'Abre el contenedor cifrado, descifra el paquete protegido y verifica quién lo firmó.',
                'Primero una capa simétrica (contenedor con contraseña), luego una asimétrica (clave privada protegida). La firma revela autoría.',
                'Caja Fuerte', 1000, '6c4f2462815dda7b8f337592af3eddc6688b26d3c415bf808d6ea6bd002a4329'],
            [5, 'Bandera a cuadros',
                'El último mensaje del responsable está cifrado "a mano", como las notas que se pasaban los estrategas antes de la radio digital. Descifrarlo revela cómo tomó el control total del servidor y cierra el caso.',
                'Rompe el criptograma con tus propias matemáticas, consigue el control del servidor y cierra la operación.',
                'Un cifrado clásico con números pequeños se puede romper. El resultado te dirá cómo llegar a lo más alto del sistema.',
                'Bandera a Cuadros', 1000, 'e10cda6471f4ffa8e3f0f74fd1fe3ab806157adef8f8aaed6e0b855655f964ff'],
        ];

        foreach ($misiones as $m) {
            Mission::updateOrCreate(['orden' => $m[0]], [
                'titulo' => $m[1], 'narrativa' => $m[2], 'objetivo' => $m[3], 'pista' => $m[4],
                'insignia' => $m[5], 'puntos' => $m[6], 'flag_hash' => $m[7],
            ]);
        }
    }
}
