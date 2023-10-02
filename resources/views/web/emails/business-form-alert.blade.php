<h2><strong>Nova conversão registrada</strong></h2>
<p><strong>Página:</strong> {{ $data['page'] ?? null }}</p>
<p><strong>Nome:</strong> {{ $data['name'] }}</p>
<p><strong>E-Mail:</strong> {{ $data['email'] }}</p>
@if (isset($data['phone']) && !empty($data['phone']))
    <p><strong>Telefone:</strong> {{ $data['phone'] }}</p>
@endif

{{-- Custom Fields --}}
@if (isset($data['company']) && !empty($data['company']))
    <p><strong>Empresa:</strong> {{ $data['company'] }}</p>
@endif
@if (isset($data['company_segment']) && !empty($data['company_segment']))
    <p><strong>Segmento:</strong> {{ $data['company_segment'] }}</p>
@endif
@if (isset($data['company_occupation']) && !empty($data['company_occupation']))
    <p><strong>Cargo:</strong> {{ $data['company_occupation'] }}</p>
@endif
@if (isset($data['company_employees']) && !empty($data['company_employees']))
    <p><strong>Nº funcionários:</strong> {{ $data['company_employees'] }}</p>
@endif
@if (isset($data['company_target']) && !empty($data['company_target']))
    <p><strong>Público alvo:</strong> {{ $data['company_target'] }}</p>
@endif
@if (isset($data['company_website']) && !empty($data['company_website']))
    <p><strong>Website:</strong> {{ $data['company_website'] }}</p>
@endif
{{-- End::Custom Fields --}}

@if (isset($data['message']) && !empty($data['message']))
    <p>{{ $data['message'] }}</p>
@endif
