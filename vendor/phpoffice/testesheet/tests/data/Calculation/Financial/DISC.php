<?php

// settlement, maturity, price, redemption, basis, result

return [
    [
        0.052420213,
        '2007-01-25',
        '2007-06-15',
        97.974999999999994,
        100,
        1,
    ],
    [
        0.01,
        '2010-04-01',
        '2015-03-31',
        95,
        100,
    ],
    [
        '#NUM!',
        '2010-04-01',
        '2015-03-31',
        0,
        100,
    ],
    [
        '#VALUE!',
        '2010-04-01',
        '2015-03-31',
        'ABC',
        100,
    ],
    [
        '#VALUE!',
        'Invalid Date',
        '2007-06-15',
        97.974999999999994,
        100,
        1,
    ],
];
