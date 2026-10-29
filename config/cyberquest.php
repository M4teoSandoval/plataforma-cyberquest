<?php

return [
    'teacher_user' => env('TEACHER_USER', 'profesor@unab.edu.co'),
    'teacher_pass' => env('TEACHER_PASS', 'monaco2025'),

    // Encuesta de satisfacción que se ofrece al completar las cinco misiones.
    // Se puede cambiar con SURVEY_URL en el .env; si queda vacía, la tarjeta
    // no aparece y /encuesta vuelve al resultado.
    'survey_url' => env('SURVEY_URL', 'https://docs.google.com/forms/d/e/1FAIpQLScuqAUY2vFobWrDKOeibvBlz2QYhXsrKaPtSMIQjDbNio1B7g/viewform?usp=sharing&ouid=102627523646836450685'),
];
