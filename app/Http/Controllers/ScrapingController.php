<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

use Illuminate\Support\Arr;

use App\Service\CrawlerService;
use App\Service\CalculatorService;
use App\Service\YahooFinanceApiService;

class ScrapingController extends Controller
{
    public function index()
    {
        $symbols  = [
            'AAPL',
            'GOOGL',
            'MSFT',
            'AMZN'
        ];

        // Dapatkan indeks simbol berdasarkan tanggal saat ini
        $index = date('z') % count($symbols);

        // Dapatkan simbol saham untuk hari ini
        $symbol = Arr::get($symbols, $index, $symbols[0]);


        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/news/';


            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('#nimbus-app')) {
                $news = $crawler->filter('div[data-testid="news-stream"] ul.stream-items li.stream-item')->each(function ($node) {
                    $titleNode = $node->filter('a.subtle-link.fin-size-small.titles.noUnderline h3.clamp');
                    $title = $titleNode->count() ? $titleNode->text() : '';

                    $linkNode = $node->filter('a.subtle-link.fin-size-small.thumb');
                    $link = $linkNode->count() ? $linkNode->attr('href') : '';

                    $thumbnailNode = $node->filter('img.ar-small');
                    $thumbnail = $thumbnailNode->count() ? $thumbnailNode->attr('src') : '';

                    if (empty($thumbnail)) {
                        $thumbnailNode = $node->filter('div.thumbBlock_holder span.thumbBlock')->first();
                        $thumbnailStyle = $thumbnailNode->count() ? $thumbnailNode->attr('style') : '';
                        preg_match('/background-image:\s*url\(([^)]+)\)/', $thumbnailStyle, $matches);
                        $thumbnail = isset($matches[1]) ? trim($matches[1], '"\'') : '';
                    }

                    if (empty($thumbnail)) {
                        $thumbnail = 'https://th.bing.com/th?id=OSK.HEROtFR4iSioh0XfS3uJJF9oXHs_YMWOG3WhSt3z4pFmzDk&w=384&h=228&c=13&rs=2&o=6&pid=SANGAM';
                    }

                    // Extracting thumbnail URL from the HTML structure
                    $thumbnailUrlNode = $node->filter('div.thumbBlock_holder span.thumbBlock')->first();
                    $thumbnailUrlStyle = $thumbnailUrlNode->count() ? $thumbnailUrlNode->attr('style') : '';
                    preg_match('/background-image:\s*url\(([^)]+)\)/', $thumbnailUrlStyle, $matches);
                    $thumbnailUrl = isset($matches[1]) ? trim($matches[1], '"\'') : '';

                    // If the thumbnail URL is not empty, use it
                    if (!empty($thumbnailUrl)) {
                        $thumbnail = $thumbnailUrl;
                    }

                    $sourceNode = $node->filter('div.publishing.font-condensed');
                    $source = $sourceNode->count() ? $sourceNode->text() : '';

                    $descriptionNode = $node->filter('p.clamp.svelte-w835pj');
                    $description = $descriptionNode->count() ? $descriptionNode->text() : '';

                    return [
                        'title' => $title,
                        'link' => $link,
                        'thumbnail' => $thumbnail,
                        'source' => $source,
                        'description' => $description
                    ];
                });

                return view('Index', [
                    'news' => $news,
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => 'Index => ' . $e->getMessage(),
            ]);
        }
    }

    public function profiles($symbol)
    {
        try {

            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('#nimbus-app')) {
                $navItems = [];

                $keywords = [
                    'Summary',
                    'News',
                    'Financials',
                    'Analysis',
                    'Statistics',
                    'Historical Data',
                    // 'Profile',
                ];


                $keywords_default = [
                    'Summary',
                    'News',
                    'Conversations',
                    'Statistics',
                    'Historical Data',
                    'Profile',
                    'Financials',
                    'Analysis',
                    'Options',
                    'Holders',
                    'Sustainability'
                ];

                foreach ($keywords as $wanted) {
                    $filteredNode = $crawler->filter('aside .nav-list li a:contains("' . $wanted . '")');

                    // Jika node yang sesuai dengan keyword ditemukan
                    if ($filteredNode->count() > 0) {
                        $navItems[] = [
                            'text' => $wanted,
                            'link' => $filteredNode->attr('href')
                        ];
                    }
                }


                $headerData = $crawler->filter('section[data-testid="quote-hdr"] .top')->each(function (Crawler $node) {
                    $exchangeInfo = $node->filter('.exchange span')->each(function (Crawler $subNode) {
                        return $subNode->text();
                    });

                    $symbolName = $node->filter('.left h1')->text();

                    return [
                        'exchange' => implode(' ', $exchangeInfo),
                        'symbolName' => $symbolName
                    ];
                });

                $priceData = $crawler->filter('section[data-testid="quote-price"]')->each(function (Crawler $node) {
                    return [
                        'livePrice' => $node->filter('.livePrice')->text() ?? 0,
                        'priceChange' => $node->filter('.priceChange')->first()->text() ?? 0,
                        'priceChangePercent' => $node->filter('.priceChange')->last()->text() ?? 0,
                        'marketTimeNotice' => $node->filter('div[slot="marketTimeNotice"] span')->text() ?? 0
                    ];
                });

                return [
                    'navItems' => $navItems,
                    'headerData' => $headerData,
                    'priceData' => $priceData,
                ];
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => 'Profiles => ' . $e->getMessage(),
            ]);
        }
    }

    public function _HandlePencarian(Request $request)
    {
        try {
            $symbol = $request->input('cari-nama');
            if ($symbol) {
                return redirect()->route('summary', ['Symbol' => $symbol]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function compare(Request $request, $symbol)
    {
        try {
            $symbolArray = $request->query('comps');
            if ($symbolArray) {
                $url = "https://finance.yahoo.com/compare/$symbol?comps=$symbolArray";
            } else {
                $url = "https://finance.yahoo.com/compare/$symbol";
            }

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('.table-container')) {
                $table_rows = $crawler->filter('table.compare-overview-table')->each(function ($table) {
                    $header = $table->filter('thead th')->each(function ($th) {
                        return $th->text();
                    });
                
                    $rows = $table->filter('tbody tr')->each(function ($tr) {
                        return $tr->filter('td')->each(function ($td) {
                            $value = $td->filter('table_rows')->count() > 0 
                                ? $td->filter('table_rows')->text() 
                                : $td->text();
                                
                            $parts = explode(' --', $value, 2);
                            return isset($parts[0]) ? trim($parts[0]) : $value;
                        });
                    });
                
                    return [
                        'labels' => $header,
                        'values' => $rows
                    ];
                });
                
                
                
                $table_grup = $crawler->filter('div.container div.grid-items div.acc-cont')->each(function ($table) {
                    return [
                        'title_tables' => $table->filter('h3.acc-header')->text(),
                        'table' => $table->filter('table')->each(function ($node) {
                            $labels = $node->filter('thead th')->each(function ($th) {
                                return $th->text();
                            });
                
                            $values = $node->filter('tbody tr')->each(function ($tr) {
                                return $tr->filter('td')->each(function ($td) {
                                    $button = $td->filter('button');
                                    $value = "";
                                    if ($button->count() > 0) {
                                        $value = $button->filter('span.value')->count() > 0 ? $button->filter('span.value')->text() : $button->text();
                                    } else {
                                        $div = $td->filter('div');
                                        if ($div->count() > 0) {
                                            $value = $div->text();
                                        } else {
                                            $value = $td->text();
                                        }
                                    }
                                    return $value === "---" ? "" : $value;
                                });
                            });
                
                            return [
                                'labels' => $labels,
                                'values' => $values
                            ];
                        }),
                    ];
                });
                

                $profiles = $this->profiles($symbol);
                // return dd($table_grup);
                return view('Pages.Compare', [
                    'table_rows' => $table_rows,
                    'table_grup' => $table_grup,
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => 'Profiles => ' . $e->getMessage(),
            ]);
        }
    }

    public function summary($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '?p=' . $symbol . '&tsrc=fin-srch';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('#nimbus-app')->count()) {

                $statistics = $crawler->filter('div[data-testid="quote-statistics"] ul li')->each(function ($node) {
                    return [
                        'label' => $node->filter('.label')->text(),
                        'value' => $node->filter('.value')->text()
                    ];
                });

                $compare = $crawler->filter('div.scroll-carousel[data-testid="carousel-container"]')
                    ->filter('section.card[data-testid="card-container"]')->each(function ($node) {
                        $ticker = $node->filter('span')->text();
                        $companyName = $node->filter('div')->text();
                        $symbol = $node->filter('a')->attr('aria-label');

                        return [
                            'ticker' => $ticker ?? null,
                            'companyName' => $companyName ?? null,
                            'symbol' => $symbol ?? null
                        ];
                    });

                $titleNode = $crawler->filter('section[data-testid="compare-to"]')->filter('h3');
                $title = $titleNode->count() ? $titleNode->text() : 'People Also Watch';

                $buttonNode = $crawler->filter('section[data-testid="compare-to"]')->filter('a[data-testid="compare-to-link"]');
                $button = $buttonNode->count() ? $buttonNode->attr('href') : null;

                $profiles = $this->profiles($symbol);


                $yahoo_services = new YahooFinanceApiService();
                $eps = $yahoo_services->eps($symbol);
                $cash_flow = $yahoo_services->cash_flow($symbol);
                $free_cash_flow = $yahoo_services->free_cash_flow($symbol);
                $deviden = $yahoo_services->deviden($symbol);
                $PeR = $yahoo_services->pe($symbol);
                $bvps = $yahoo_services->book_value_per_share($symbol);
                $solvabilitas = $yahoo_services->solvabilitas($symbol);

                

                return view('Pages.Summary', [
                    'symbol' => $symbol,
                    'compareTitle' => $title,
                    'compareButton' => $button,
                    'compare' => $compare,
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'statistics' => $statistics,
                    'eps' => $eps,
                    'cash_flow' => $cash_flow,
                    'free_cash_flow' => $free_cash_flow,
                    'deviden' => $deviden,
                    'PeR' => $PeR,
                    'bvps' => number_format($bvps, 2),
                    'debt_to_equity_ratio' => number_format($solvabilitas['debt_to_equity_ratio'], 2),
                    'equity_ratio' => number_format($solvabilitas['equity_ratio'], 2)
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function news($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/news/';


            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('#nimbus-app')) {

                $response = null;
                $news = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $news = $crawler->filter('div[data-testid="news-stream"] ul.stream-items li.stream-item')->each(function ($node) {

                        $titleNode = $node->filter('a.subtle-link.fin-size-small.titles.noUnderline h3.clamp');
                        $title = $titleNode->count() ? $titleNode->text() : '';

                        $linkNode = $node->filter('a.subtle-link.fin-size-small.thumb');
                        $link = $linkNode->count() ? $linkNode->attr('href') : '';

                        $thumbnailNode = $node->filter('img.ar-small');
                        $thumbnail = $thumbnailNode->count() ? $thumbnailNode->attr('src') : 'https://th.bing.com/th?id=OSK.HEROtFR4iSioh0XfS3uJJF9oXHs_YMWOG3WhSt3z4pFmzDk&w=384&h=228&c=13&rs=2&o=6&pid=SANGAM';

                        $sourceNode = $node->filter('div.publishing.font-condensed');
                        $source = $sourceNode->count() ? $sourceNode->text() : '';


                        $descriptionNode = $node->filter('p.clamp.svelte-w835pj');
                        $description = $descriptionNode->count() ? $descriptionNode->text() : '';

                        return [
                            'title' => $title,
                            'link' => $link,
                            'thumbnail' => $thumbnail,
                            'source' => $source,
                            'description' => $description
                        ];
                    });
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.News', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'news' => $news,
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function chart($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/chart/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('#nimbus-app')) {

                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response['title'] = $crawler;
                }
                $profiles = $this->profiles($symbol);

                return view('Pages.Chart', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function community($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/community/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('#nimbus-app')) {

                $response = null;
                $community = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $h3Node = $crawler->filter('h3.Typography__text--11-4-6.Typography__product-sub-heading--11-4-6.Typography__l5--11-4-6.spcv_subheader-container span');
                    $community = $h3Node->text();
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Community', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'community' => $community
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function history($symbol)
    {
        try {
            $periode2 = time();
            $periode1 = strtotime('-5 years', $periode2);
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/history/?period1='.$periode1.'&period2='. $periode2;

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('#nimbus-app')) {

                $response = null;
                $columnTitles = null;
                $rowData = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $columnTitles = $crawler->filter('[data-testid="history-table"] thead th')->each(function ($node) {
                        return $node->text();
                    });

                    $rowData = $crawler->filter('[data-testid="history-table"] tbody tr')->each(function ($node) {
                        $rowData = [];
                        $node->filter('td')->each(function ($tdNode) use (&$rowData) {
                            $rowData[] = $tdNode->text();
                        });
                        return $rowData;
                    });
                }


                $profiles = $this->profiles($symbol);

                return view('Pages.History', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'columnTitles' => $columnTitles,
                    'rowData' => $rowData,
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function options($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/options/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('body')->count() > 0) {
                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = 'Hi, it looks like something good has already happened. Please double-check your code.';
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Options', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function components($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/components/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('body')) {

                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = 'Hi, it looks like something good has already happened. Please double-check your code.';
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Components', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function profile($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/profile/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('body')) {

                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = $crawler->filter('section.main.svelte-e2c64s section section')->each(function ($item) {
                        return $item->text();
                    });
                }

                $profiles = $this->profiles($symbol);
                $summary = $this->summary($symbol);


                return view('Pages.Profile', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response,
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function statistics($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/key-statistics/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('body')) {

                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = $crawler->filter('div.table-container.yf-104jbnt table tbody tr')->each(function ($item) {
                        $label = $item->filter('td:first-child')->text();
                        $values = $item->filter('td:not(:first-child)')->each(function ($value) {
                            return $value->text();
                        });
                    
                        return [
                            'label' => $label,
                            'values' => $values,
                        ];
                    });

                    $financial_highlights = $crawler->filter('section.yf-14j5zka')->each(function ($section) {
                        $header = $section->filter('header h3.header')->text();
                    
                        // Ambil semua section card
                        $cards = $section->filter('section.card')->each(function ($card) {
                            $title = $card->filter('header h3.title')->text();
                            $rows = $card->filter('table tbody tr')->each(function ($row) {
                                $label = trim($row->filter('td.label')->text());
                                $value = trim($row->filter('td.value')->text());
                    
                                // Hapus bagian "--" dari nilai jika ada
                                $value = preg_replace('/\s*--\s*$/', '', $value);
                    
                                return [
                                    'label' => $label,
                                    'value' => $value
                                ];
                            });
                    
                            return [
                                'title' => $title,
                                'data' => $rows
                            ];
                        });
                    
                        return [
                            'header' => $header,
                            'cards' => $cards
                        ];
                    });
                }

                $profiles = $this->profiles($symbol);
                // return dd($financial_highlights);
                return view('Pages.Key-statistics', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response,
                    'financial_highlights' => $financial_highlights
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function financials($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('body')) {

                $response = null;
                $navLink = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = $crawler->filter('section.container.yf-1pgoo1f')->each(function ($section) {
                        $table = $section->filter('div.table.yf-1pgoo1f')->first();
                    
                        $labels = $table->filter('div.tableHeader.yf-1pgoo1f div.row.yf-1ezv2n5')->first()->filter('div.column.yf-1ezv2n5')->each(function ($column) {
                            return $column->text();
                        });
                    
                        $rows = $table->filter('div.tableBody.yf-1pgoo1f div.row.lv-0.yf-1xjz32c')->each(function ($row) {
                            $rowData = $row->filter('div.column.yf-1xjz32c')->each(function ($column) {
                                return $column->text();
                            });
                            return $rowData;
                        });
                    
                        return [
                            'labels' => $labels,
                            'values' => $rows,
                        ];
                    });
                    

                    $navLink = $crawler->filter('nav[aria-label="financials"] ul.nav-list li a')->each(function ($node) {
                        if (trim($node->text()) !== 'Dividends' && $node->attr('href') !== '/quote/AAPL/dividends') {
                            return [
                                'text' => trim($node->text()),
                                'href' => $node->attr('href')
                            ];
                        }
                        return null;
                    });

                    $navLink = array_filter($navLink);
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Financials', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'navLink' => $navLink,
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function balancesheet($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/balance-sheet/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('body')) {

                $response = null;
                $navLink = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = $crawler->filter('section.container.yf-1pgoo1f')->each(function ($section) {
                        $table = $section->filter('div.table.yf-1pgoo1f')->first();
                    
                        $labels = $table->filter('div.tableHeader.yf-1pgoo1f div.row.yf-1ezv2n5')->first()->filter('div.column.yf-1ezv2n5')->each(function ($column) {
                            return $column->text();
                        });
                    
                        $rows = $table->filter('div.tableBody.yf-1pgoo1f div.row.lv-0.yf-1xjz32c')->each(function ($row) {
                            $rowData = $row->filter('div.column.yf-1xjz32c')->each(function ($column) {
                                return $column->text();
                            });
                            return $rowData;
                        });
                    
                        return [
                            'labels' => $labels,
                            'values' => $rows,
                        ];
                    });
                    

                    $navLink = $crawler->filter('nav[aria-label="financials"] ul.nav-list li a')->each(function ($node) {
                        if (trim($node->text()) !== 'Dividends' && $node->attr('href') !== '/quote/AAPL/dividends') {
                            return [
                                'text' => trim($node->text()),
                                'href' => $node->attr('href')
                            ];
                        }
                        return null;
                    });

                    $navLink = array_filter($navLink);
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Balance-sheet', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'navLink' => $navLink,
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cashflow($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/cash-flow/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('body')) {

                $response = null;
                $navLink = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = $crawler->filter('section.container.yf-1pgoo1f')->each(function ($section) {
                        $table = $section->filter('div.table.yf-1pgoo1f')->first();
                    
                        $labels = $table->filter('div.tableHeader.yf-1pgoo1f div.row.yf-1ezv2n5')->first()->filter('div.column.yf-1ezv2n5')->each(function ($column) {
                            return $column->text();
                        });
                    
                        $rows = $table->filter('div.tableBody.yf-1pgoo1f div.row.lv-0.yf-1xjz32c')->each(function ($row) {
                            $rowData = $row->filter('div.column.yf-1xjz32c')->each(function ($column) {
                                return $column->text();
                            });
                            return $rowData;
                        });
                    
                        return [
                            'labels' => $labels,
                            'values' => $rows,
                        ];
                    });
                    

                    $navLink = $crawler->filter('nav[aria-label="financials"] ul.nav-list li a')->each(function ($node) {
                        if (trim($node->text()) !== 'Dividends' && $node->attr('href') !== '/quote/AAPL/dividends') {
                            return [
                                'text' => trim($node->text()),
                                'href' => $node->attr('href')
                            ];
                        }
                        return null;
                    });

                    $navLink = array_filter($navLink);
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Cash-flow', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'navLink' => $navLink,
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function analysis($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/analysis?p=' . $symbol . '';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);

            if ($crawler->filter('body')) {
                $response = [];

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('#message-1')->text();
                } else {
                    $estimates = [
                        'earningsEstimate' => 'Earnings Estimate',
                        'revenueEstimate' => 'Revenue Estimate',
                        'epsTrend' => 'EPS Trend',
                        'epsRevisions' => 'EPS Revisions',
                        'growthEstimate' => 'Growth Estimate',
                    ];

                    foreach ($estimates as $key => $label) {
                        $section = $crawler->filter('section[data-testid="' . $key . '"]');
                        if ($section->count() > 0) {
                            // Check if the section contains a table element
                            if ($section->filter('table')->count() > 0) {
                                $response[$key]['labels'] = $section->filter('table thead th')->each(function ($label) {
                                    return $label->text();
                                });

                                $response[$key]['values'] = $section->filter('table tbody tr')->each(function ($row) {
                                    $rowData = [];
                                    $row->filter('td')->each(function ($cell) use (&$rowData) {
                                        $rowData[] = $cell->text();
                                    });
                                    return $rowData;
                                });
                            } else {
                                // If the section does not contain a table, assume tr-based structure
                                $response[$key]['labels'] = [];
                                $response[$key]['values'] = $section->filter('tr')->each(function ($row) {
                                    $rowData = [];
                                    $row->filter('td')->each(function ($cell) use (&$rowData) {
                                        $rowData[] = $cell->text();
                                    });
                                    return $rowData;
                                });
                            }
                        } else {
                            // Handle the case where the section is not found
                            $response[$key] = ['labels' => [], 'values' => []];
                        }
                    }
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Analysis', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function holders($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/holders/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('body')) {

                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = 'Hi, it looks like something good has already happened. Please double-check your code.';
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Holders', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sustainability($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/sustainability/';

            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('body')) {

                $response = null;

                if ($crawler->filter('#message-1')->count() > 0) {
                    $response['error'] = $crawler->filter('tbody')->each(function ($node) {
                        return [
                            'img' => $node->filter('img')->attr('src'),
                            'h1' => $node->filter('h1')->text(),
                            'text1' => $node->filter('#message-1')->text(),
                            'text2' => $node->filter('#message-2')->text(),
                        ];
                    });
                } else {
                    $response = 'Hi, it looks like something good has already happened. Please double-check your code.';
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Sustainability', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'response' => $response
                ]);
            } else {
                throw new \Exception("Element not found");
            }
        } catch (\Exception $e) {
            return view('Components.PageError', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
