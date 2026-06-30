<?php

use Illuminate\Support\Facades\Validator;

test('validates Peru DNI correctly', function () {
    $rules = [
        'document_number' => [
            function (string $attribute, $value, Closure $fail) {
                if (!preg_match('/^[0-9]{8}$/', $value)) {
                    $fail('El DNI debe tener exactamente 8 números.');
                }
            }
        ]
    ];

    $passData = ['document_number' => '12345678'];
    $failData1 = ['document_number' => '1234567'];
    $failData2 = ['document_number' => '1234567a'];

    expect(Validator::make($passData, $rules)->passes())->toBeTrue();
    expect(Validator::make($failData1, $rules)->passes())->toBeFalse();
    expect(Validator::make($failData2, $rules)->passes())->toBeFalse();
});

test('validates Peru RUC correctly', function () {
    $rules = [
        'document_number' => [
            function (string $attribute, $value, Closure $fail) {
                if (!preg_match('/^(10|15|17|20)[0-9]{9}$/', $value)) {
                    $fail('El RUC debe tener 11 números y comenzar con 10, 15, 17 o 20.');
                }
            }
        ]
    ];

    $passData = ['document_number' => '20601234567'];
    $failData1 = ['document_number' => '11223344556'];
    $failData2 = ['document_number' => '2060123456'];

    expect(Validator::make($passData, $rules)->passes())->toBeTrue();
    expect(Validator::make($failData1, $rules)->passes())->toBeFalse();
    expect(Validator::make($failData2, $rules)->passes())->toBeFalse();
});

test('validates Chile RUN/RUT correctly', function () {
    $rules = [
        'document_number' => [
            function (string $attribute, $value, Closure $fail) {
                $cleanRun = strtolower(preg_replace('/[^0-9kK]/', '', $value));
                if (strlen($cleanRun) < 8 || strlen($cleanRun) > 9) {
                    $fail('El RUN/RUT debe tener entre 8 y 9 dígitos (ej: 12345678-k).');
                    return;
                }
                
                $num = substr($cleanRun, 0, -1);
                $dv = substr($cleanRun, -1);
                
                $sum = 0;
                $mul = 2;
                for ($i = strlen($num) - 1; $i >= 0; $i--) {
                    $sum += $num[$i] * $mul;
                    $mul = ($mul === 7) ? 2 : $mul + 1;
                }
                $res = 11 - ($sum % 11);
                $calcDv = ($res === 11) ? '0' : (($res === 10) ? 'k' : (string)$res);
                
                if ($dv !== $calcDv) {
                    $fail('El RUN/RUT ingresado es inválido.');
                }
            }
        ]
    ];

    // 12.345.678-5 / 12345678-5 es válido (la fórmula da 5)
    expect(Validator::make(['document_number' => '12.345.678-5'], $rules)->passes())->toBeTrue();
    expect(Validator::make(['document_number' => '12345678-5'], $rules)->passes())->toBeTrue();
    
    // RUTs inválidos
    expect(Validator::make(['document_number' => '12345678-k'], $rules)->passes())->toBeFalse(); // Debería ser 5
    expect(Validator::make(['document_number' => '12345'], $rules)->passes())->toBeFalse();
});
