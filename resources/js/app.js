import './bootstrap'; // 🚨 Esta es la línea que NO debe faltar

import 'bootstrap/dist/css/bootstrap.min.css';     // Bootstrap CSS
import * as bootstrap from 'bootstrap';            // Bootstrap JS
window.bootstrap = bootstrap;                      // Hacerlo accesible globalmente

import '../css/app.css'; // Ruta corregida al CSS personalizado
