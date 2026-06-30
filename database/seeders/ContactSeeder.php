<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->warn('No se encontraron empresas para asociar los contactos.');
            return;
        }

        foreach ($companies as $company) {
            // Contacto 1: Cliente de Perú con DNI (Persona Natural)
            $contact1 = Contact::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'document_type' => 'dni',
                    'document_number' => '45678912',
                ],
                [
                    'name' => 'Juan Carlos Pérez Ramos',
                    'type' => 'customer',
                    'legal_type' => 'person',
                    'email' => 'juan.perez@email.com',
                    'phone' => '+51 987654321',
                    'billing_payment_terms' => 'cash',
                    'credit_limit' => 0.0000,
                    'is_active' => true,
                ]
            );

            $contact1->addresses()->updateOrCreate(
                ['label' => 'Casa Principal'],
                [
                    'type' => 'fiscal',
                    'address' => 'Calle Las Magnolias 456, Dpto 302',
                    'city' => 'Miraflores',
                    'state_region' => 'Lima',
                    'country' => 'PE',
                    'is_default' => true,
                ]
            );

            // Contacto 2: Proveedor de Perú con RUC (Persona Jurídica)
            $contact2 = Contact::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'document_type' => 'ruc',
                    'document_number' => '20601234567',
                ],
                [
                    'name' => 'Distribuidora Comercial Joral S.A.C.',
                    'type' => 'supplier',
                    'legal_type' => 'company',
                    'email' => 'contacto@comercialjoral.com.pe',
                    'phone' => '+51 999888777',
                    'billing_payment_terms' => 'credit_30',
                    'credit_limit' => 5000.0000,
                    'is_active' => true,
                ]
            );

            $contact2->addresses()->updateOrCreate(
                ['label' => 'Almacén Callao'],
                [
                    'type' => 'fiscal',
                    'address' => 'Av. Argentina 2450, Urb. Industrial',
                    'city' => 'Callao',
                    'state_region' => 'Lima',
                    'country' => 'PE',
                    'is_default' => true,
                ]
            );

            // Contacto 3: Cliente de Chile con RUN (Persona Natural)
            $contact3 = Contact::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'document_type' => 'run',
                    'document_number' => '12345678-5',
                ],
                [
                    'name' => 'María Alejandra González Silva',
                    'type' => 'customer',
                    'legal_type' => 'person',
                    'email' => 'maria.gonzalez@email.cl',
                    'phone' => '+56 9 8765 4321',
                    'billing_payment_terms' => 'cash',
                    'credit_limit' => 0.0000,
                    'is_active' => true,
                ]
            );

            $contact3->addresses()->updateOrCreate(
                ['label' => 'Domicilio Residencial'],
                [
                    'type' => 'fiscal',
                    'address' => 'Av. Providencia 1250, Apto 510',
                    'city' => 'Providencia',
                    'state_region' => 'Santiago',
                    'country' => 'CL',
                    'is_default' => true,
                ]
            );

            // Contacto 4: Cliente/Proveedor (Ambos) de Chile con RUT (Persona Jurídica)
            $contact4 = Contact::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'document_type' => 'rut',
                    'document_number' => '19234567-k',
                ],
                [
                    'name' => 'Sociedad Importadora del Pacífico SpA',
                    'type' => 'both',
                    'legal_type' => 'company',
                    'business_activity' => 'Importadora de repuestos y maquinarias',
                    'email' => 'importaciones@pacificochile.cl',
                    'phone' => '+56 2 2345 6789',
                    'billing_payment_terms' => 'credit_60',
                    'credit_limit' => 15000.0000,
                    'is_active' => true,
                ]
            );

            $contact4->addresses()->updateOrCreate(
                ['label' => 'Oficina Central'],
                [
                    'type' => 'fiscal',
                    'address' => 'Av. Apoquindo 4400, Piso 12',
                    'city' => 'Las Condes',
                    'state_region' => 'Santiago',
                    'country' => 'CL',
                    'is_default' => true,
                ]
            );

            $contact4->addresses()->updateOrCreate(
                ['label' => 'Sucursal Valparaíso'],
                [
                    'type' => 'shipping',
                    'address' => 'Calle Prat 725, Oficina 402',
                    'city' => 'Valparaíso',
                    'state_region' => 'Valparaíso',
                    'country' => 'CL',
                    'is_default' => false,
                ]
            );
        }

        $this->command->info('✅ Contactos y direcciones de prueba sembrados correctamente.');
    }
}
