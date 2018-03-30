/*$('.tooltip').bt(
    {
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
    centerPointX: 0.5,
    centerPointY: 0.5
    }
);*/

$('.tooltip').bt("Example content to test out",{
  trigger: 'hover',
  //contentSelector: "$(this).attr('title')",
  fill: 'red',
  positions: ['left', 'right', 'bottom']
});

$('.imgover').bt("Example content to test out",{
  trigger: 'hover',
  //contentSelector: "$(this).attr('title')",
  fill: 'red',
  positions: ['left', 'right', 'bottom']
});

