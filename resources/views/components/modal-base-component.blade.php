<div
    x-data="{
        show_this: @entangle('show')
    }"
    x-cloak

    x-show="show_this"

    x-on:keydown.escape.window="show_this = false"
    class="fixed inset-0 overflow-y-auto px-4 py-6 md:py-24 sm:px-0 z-40"
>

    <div x-show="show_this" class="fixed inset-0 transform" x-on:click="show_this = false">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>



    <div x-show="show_this"
        class="bg-white rounded-lg overflow-hidden transform sm:w-full sm:mx-auto max-w-md"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
        >

        <div class="flex pr-2 pb-1 bg-grey-darkest text-white">

            <div class="flex-grow text-2xl px-4 py-2">
                Edit Contact
            </div>

            <div class="inline-block text-4xl cursor-pointer text-white" @click="show_this = false">
                &times;
            </div>

        </div>


        {{ $slot }} <!-- Specific modal goes in here -->

    </div>
</div>