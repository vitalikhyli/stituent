<div class="flex">
    <div class="w-full border-b-4">
        <div class="font-bold text-3xl">
            State Government Twitter Feeds
        </div>
        
    </div>
</div>
<div class="flex text-lg text-grey-dark">
    <div class="w-1/2 pt-6 pr-16 ">
        
        This feed updates Live and contains as many active Twitter accounts from Senators and Representatives as we could find. 
        
        
    </div>
    <div class="w-1/2 pl-8 pt-6">
        Check here for breaking news, reactions, and personal updates directly from your colleagues.
    </div>
</div>

<div class="flex mt-4">
    @php
        $whou = 'w-1/2';
        $wsen = 'w-1/4';
        if (Auth::user()->isSenate()) {
            $whou = 'w-1/4';
            $wsen = 'w-1/2';
        }
    @endphp
    <div class="{{ $whou }} border-r-4">
        
        <a class="twitter-timeline" href="https://twitter.com/Constituent_1st/lists/massachusetts-house?ref_src=twsrc%5Etfw">House List</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        
    </div>
    <div class="{{ $wsen }} border-r-4">
        <a class="twitter-timeline" href="https://twitter.com/Constituent_1st/lists/massachusetts-senate?ref_src=twsrc%5Etfw">Senate List</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </div>
    <div class="w-1/4">
        <a class="twitter-timeline" href="https://twitter.com/Constituent_1st/lists/massachusetts-executive?ref_src=twsrc%5Etfw">Executive Offices</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </div>
</div>