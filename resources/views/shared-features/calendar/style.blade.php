<style>

	#calendar .day .day-number {
		padding-top: 6px;
		/*font-family: sans-serif;*/
	}
	#calendar .day.event .day-number {
		border: 2px solid lightgray;
		background: #eee;
		color: #3490dc;
		cursor: pointer;
	} 
	#calendar .day.event .day-number:hover {
		/*background: #ddd;*/
	} 
	#calendar .day.today .day-number {
		font-weight: bold;
		color: white;
		border: 2px solid white;
		background: #4299e1;
	}
	#calendar .day.selected .day-number {
		font-weight: bold;
		/*color: black;*/
		padding-top: 4px;
		border: 4px solid #3490dc;
	}
	#calendar .day.next-month .day-number,
	#calendar .day.last-month .day-number {
		color: lightgray;
	} 
	#calendar .clndr-next-button,
	#calendar .clndr-previous-button {
		cursor: pointer;
		transition: 0s;
	}
	#events-by-date .table>tbody>tr>td {
		border-top: 0px;
	}

</style>