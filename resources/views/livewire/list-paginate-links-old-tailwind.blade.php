
@if ($paginator->hasPages())

<nav class="border-t border-gray px-4 flex items-center justify-between sm:px-0">
  <div class="w-0 flex-1 flex">
     @if (!$paginator->onFirstPage())
        <a wire:click="previousPage" class="cursor-pointer -mt-px border-t-2 border-transparent pt-4 pr-1 inline-flex items-center text-sm leading-5 font-medium text-gray hover:text-gray hover:border-gray-300 focus:outline-none focus:text-gray focus:border-gray transition ease-in-out duration-150">
          <svg class="mr-3 h-5 w-5 text-gray" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
          </svg>
          <span class="hidden md:block">Previous</span>
        </a>
    @endif
  </div>
  <div class="overflow-hidden">

     @foreach ($elements as $element)

        @if (is_string($element))
            <span class="-mt-px border-t-2 border-transparent pt-4 px-4 inline-flex items-center text-sm leading-5 font-medium text-gray">
              ...
            </span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                     <a wire:click="gotoPage({{ $page }})" class="-mt-px border-t-4 font-bold pt-4 px-4 inline-flex items-center text-sm leading-5 font-medium text-gray hover:text-gray hover:border-gray-300 focus:outline-none focus:text-gray focus:border-gray transition ease-in-out duration-150">
                      {{ $page }}
                    </a>
                @else
                     <a wire:click="gotoPage({{ $page }})" class="cursor-pointer -mt-px border-t-4 border-transparent pt-4 px-4 inline-flex items-center text-sm leading-5 font-medium text-gray hover:text-gray hover:border-gray-300 focus:outline-none focus:text-gray focus:border-gray transition ease-in-out duration-150">
                      {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
        
    @endforeach
   
  </div>
  <div class="w-0 flex-1 flex justify-end">
    @if ($paginator->hasMorePages())
        <a wire:click="nextPage" class="cursor-pointer -mt-px border-t-2 border-transparent pt-4 pl-1 inline-flex items-center text-sm leading-5 font-medium text-gray hover:text-gray hover:border-gray-300 focus:outline-none focus:text-gray focus:border-gray transition ease-in-out duration-150">
          <span class="hidden md:block">Next</span>
          <svg class="ml-3 h-5 w-5 text-gray" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </a>
    @endif
  </div>

</nav>


@endif
