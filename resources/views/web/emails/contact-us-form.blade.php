<h2><strong>Contato via website</strong></h2>
<p><strong>Página:</strong> {{ $data['page'] ?? null }}</p>
<p><strong>Nome:</strong> {{ $data['name'] }}</p>
<p><strong>E-Mail:</strong> {{ $data['email'] }}</p>
@if (isset($data['phone']) && !empty($data['phone']))
    <p><strong>Telefone:</strong> {{ $data['phone'] }}</p>
@endif
@if (isset($data['subject']) && !empty($data['subject']))
    <p><strong>Assunto:</strong> {{ $data['subject'] }}</p>
@endif
@if (isset($data['message']) && !empty($data['message']))
    <p>{{ $data['message'] }}</p>
@endif
