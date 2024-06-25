<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

use App\Service\CrawlerService;


class ScrapingController extends Controller
{
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
                    'Profile',
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

    public function summary($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '?p=' . $symbol . '&tsrc=fin-srch';


            $crawler_service = new CrawlerService;
            $crawler = $crawler_service->main($url);


            if ($crawler->filter('#nimbus-app')) {

                $statistics = $crawler->filter('div[data-testid="quote-statistics"] ul li')->each(function ($node) {
                    return [
                        'label' => $node->filter('.label')->text(),
                        'value' => $node->filter('.value')->text()
                    ];
                });

                $profiles = $this->profiles($symbol);

                return view('Pages.Summary', [
                    'navItems' => $profiles['navItems'],
                    'headerData' => $profiles['headerData'],
                    'priceData' => $profiles['priceData'],
                    'statistics' => $statistics,
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
                        $thumbnail = $thumbnailNode->count() ? $thumbnailNode->attr('src') : '';

                        $sourceNode = $node->filter('div.publishing.font-condensed');
                        $source = $sourceNode->count() ? $sourceNode->text() : '';

                        return [
                            'title' => $title,
                            'link' => $link,
                            'thumbnail' => $thumbnail,
                            'source' => $source,
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
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/history/';

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
                    $response = $crawler->filter('section.main.svelte-e2c64s section')->each(function ($item) {
                        return $item->text();
                    });
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Profile', [
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
                    $response = $crawler->filter('div.table-container.svelte-104jbnt table tbody tr')->each(function ($item) {
                        $label = $item->filter('td:first-child')->text();
                        $value = $item->filter('td:last-child')->text();
                        return [
                            'label' => $label,
                            'value' => $value,
                        ];
                    });
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Key-statistics', [
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

    public function financials($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

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
                    $response = $crawler->filter('div.tableContainer.svelte-1pgoo1f div.table.svelte-1pgoo1f')->each(function ($table) {
                        $labels = $table->filter('div.tableHeader.svelte-1pgoo1f')->first()->filter('div.column.svelte-1ezv2n5')->each(function ($column) {
                            return $column->text();
                        });


                        $values = $table->filter('div.tableBody.svelte-1pgoo1f div.row.lv-0.svelte-1xjz32c')->each(function ($row) {
                            return $row->filter('div.column.svelte-1xjz32c')->each(function ($column) {
                                return $column->text();
                            });
                        });

                        return [
                            'labels' => $labels,
                            'values' => $values,
                        ];
                    });
                }

                $profiles = $this->profiles($symbol);

                return view('Pages.Financials', [
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

    public function analysis($symbol)
    {
        try {
            $url = 'https://finance.yahoo.com/quote/' . $symbol . '/analysis?p=' . $symbol . '';

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
                    $response['labels'] = $crawler->filter('section[data-testid="earningsEstimate"] table thead th')->each(function ($label) {
                        return $label->text();
                    });

                    // Mendapatkan data dari tabel
                    $response['values'] = $crawler->filter('section[data-testid="earningsEstimate"] table tbody tr')->each(function ($row) {
                        $rowData = [];
                        $row->filter('td')->each(function ($cell) use (&$rowData) {
                            $rowData[] = $cell->text();
                        });
                        return $rowData;
                    });
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
