<div
    x-data="{ started: false }"
    x-show="!started"
    @click="started = true"
    class="fixed inset-0 z-50 bg-cover bg-center cursor-pointer"
    style="background-image: url('{{ asset('css/images/guest_bg.jpg') }}')"
>

</div>
