
<div class="container mx-auto grid xs:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 pt-6 gap-8">
@if($games === NULL)
    <p>No games found.</p>
@else
@foreach($games as $game)
    <a href="launcher?slug={{ $game->slug }}" class="group-hover:mb-8">
        <img class="object-cover rounded-md h-20 border-gray-300 group-hover:mb-8 dark:border-gray-700 border-dashed border-2 rounded" src="https://cdn2.softswiss.net/i/s2/{{ $game->gid }}.webp"/>
    </a>
@endforeach
@endif
</div>