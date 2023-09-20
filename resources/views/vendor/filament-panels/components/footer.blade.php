{{-- fixed  --}}
<footer
    class="bottom-0 left-0 z-20 w-full p-4 bg-white border-t border-gray-200 shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-gray-800 dark:border-gray-600">
    <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
        © {{ date('Y') > '2010' ? '2010 - ' . date('Y') : '2010' }} - InCloudCodile14</a> //
        {{ __('desenvolvido por:') }} <a href="https://incloudsistemas.com.br" target="_blank"
            class="hover:underline">incloudsistemas.com.br</a> - {{ __('Todos os direitos reservados') }}.
    </span>
</footer>
