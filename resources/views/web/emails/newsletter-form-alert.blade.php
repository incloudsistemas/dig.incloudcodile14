<h2><strong>Nova inscrição na newsletter</strong></h2>
<p><strong>Página:</strong> {{ $data['page'] ?? null }}</p>
<p><strong>Nome:</strong> {{ $data['name'] }}</p>
<p><strong>E-Mail:</strong> {{ $data['email'] }}</p>
@if (isset($data['phone']) && !empty($data['phone']))
    <p><strong>Telefone:</strong> {{ $data['phone'] }}</p>
@endif
