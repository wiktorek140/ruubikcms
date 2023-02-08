$(document).ready(function(){
	$('.tooltip').bt({
		trigger: 'click',
		positions: ['bottom','top'],
		cssClass: 'tooltipBox',
		closeWhenOthersOpen: true,
		fill: '#e5e5e5',
		width: '220px',
		padding: '10px',
		spikeGirth: 20, 
		spikeLength: 8,
		overlap: -2,
		centerPointX: .5,
		centerPointY: .5
	});
});