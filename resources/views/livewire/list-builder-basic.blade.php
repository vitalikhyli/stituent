<div class="flex text-grey-dark">

	<div class="w-1/4"></div>

	<div class="w-3/4">

<!-- 		<div class="flex pt-1">

			<div class="relative uppercase text-sm pt-2">
				<label for="include_deceased" class="font-normal">
					<input type="checkbox"
						   id="include_deceased"
						   wire:model="input.include_deceased"
						   value="1" 
						    /> <span class="px-2">Include Deceased Voters?</span>

				</label>
			</div>

		</div> -->

		<div class="flex pt-1">

			<div class="relative uppercase text-sm pt-2">
				<label for="include_archived" class="font-normal">
					<input type="checkbox"
						   id="include_archived"
						   wire:model="input.include_archived"
						   value="1" 
						    /> <span class="px-2">Include Archived Voters?</span>

				</label>
			</div>

		</div>		

	</div>

</div>