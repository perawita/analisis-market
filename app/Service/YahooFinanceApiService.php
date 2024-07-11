<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

use DateTime;

class YahooFinanceApiService extends CrawlerService
{

    protected function live_price($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/';
        $crawler = $this->main($url);
        
        return (float)str_replace(',', '', $crawler->filter('.livePrice')->text() ?? 0);;
    }
    
    public function eps($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '?p=' . $symbol . '&tsrc=fin-srch';
        $crawler = $this->main($url);
    
        $response = $crawler->filter('div[data-testid="quote-statistics"] ul li')->each(function ($node) {
            return [
                'label' => $node->filter('span:nth-child(1)')->text(),
                'value' => $node->filter('span:nth-child(2)')->text()
            ];
        });
    
        $index = array_search("EPS (TTM)", array_column($response, 'label'));
    
        // Jika ditemukan, ambil nilai "EPS (TTM)"
        if ($index !== false) {
            $eps_value = $response[$index]['value'];
            return (float)str_replace(',', '', $eps_value); // Menghapus koma jika ada dan mengkonversi ke float
        } else {
            throw new \Exception("EPS (TTM) data not found.");
        }
    }
        
    public function pe($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '?p=' . $symbol . '&tsrc=fin-srch';
        $crawler = $this->main($url);
    
        $response = $crawler->filter('div[data-testid="quote-statistics"] ul li')->each(function ($node) {
            return [
                'label' => $node->filter('span:nth-child(1)')->text(),
                'value' => $node->filter('span:nth-child(2)')->text()
            ];
        });
    
        $index = array_search("PE Ratio (TTM)", array_column($response, 'label'));
    
        // Jika ditemukan, ambil nilai "PE Ratio (TTM)"
        if ($index !== false) {
            $eps_value = $response[$index]['value'];
            return (float)str_replace(',', '', $eps_value); // Menghapus koma jika ada dan mengkonversi ke float
        } else {
            throw new \Exception("PE Ratio (TTM) data not found.");
        }
    }

    protected function eps_five_year_ago($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';
        $crawler = $this->main($url);
    
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
        

        $index = array_search("Basic EPS", array_column($response[0]['values'], 0));

        // Jika ditemukan, ambil nilai "Basic EPS"
        if ($index !== false) {
            $basic_eps_values = array_slice($response[0]['values'][$index], 1); 
            return $basic_eps_values;
        }
    }

    protected function eps_year_of_year($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';
        $crawler = $this->main($url);

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

        // Cari indeks untuk "Basic EPS"
        $index = null;
        foreach ($response[0]['values'] as $key => $value) {
            if (isset($value[0]) && $value[0] == "Basic EPS") {
                $index = $key;
                break;
            }
        }

        // Jika ditemukan, ambil nilai "Basic EPS"
        if ($index !== null) {
            $basic_eps_values = array_slice($response[0]['values'][$index], 1);

            // Ambil EPS dari lima tahun yang lalu, asumsi bahwa data diurutkan dari yang terbaru
            if (count($basic_eps_values) >= 5) {
                return $basic_eps_values[count($basic_eps_values) - 5];
            } else {
                throw new \Exception("Not enough data to retrieve EPS from five years ago.");
            }
        } else {
            throw new \Exception("Basic EPS data not found.");
        }
    }


    protected function cash_flow_one_year_ago($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

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

        
        $desired_labels = ["Diluted EPS"];
        $index = false;
        
        // Cari indeks dari label yang diinginkan
        foreach ($desired_labels as $label) {
            $index = array_search($label, array_column($response[0]['values'], 0));
            if ($index !== false) {
                break;
            }
        }
        
        // Jika ditemukan, ambil nilai "Operating Cash Flow"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 1);
            
            $valid_cash_flow_values = [];
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }
        
            // Ambil nilai ke-2 jika ada, jika tidak, kembalikan 0.00
            return $valid_cash_flow_values[1] ?? 0.00;
        }
        
        return 0.00; // Kembalikan 0.00 jika label tidak ditemukan
        
    }

    protected function cash_flow_now($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

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

        
        $desired_labels = ["Diluted EPS"];
        $index = false;
        
        // Cari indeks dari label yang diinginkan
        foreach ($desired_labels as $label) {
            $index = array_search($label, array_column($response[0]['values'], 0));
            if ($index !== false) {
                break;
            }
        }
        
        // Jika ditemukan, ambil nilai "Operating Cash Flow"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 1);
            
            $valid_cash_flow_values = [];
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }
        
            // Ambil nilai ke-2 jika ada, jika tidak, kembalikan 0.00
            return $valid_cash_flow_values[0] ?? 0.00;
        }
        
        return 0.00; // Kembalikan 0.00 jika label tidak ditemukan
        
    }

    protected function cash_flow_two_year_ago($symbol)
    {

        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

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

        
        $desired_labels = ["Diluted EPS"];
        $index = false;
        
        // Cari indeks dari label yang diinginkan
        foreach ($desired_labels as $label) {
            $index = array_search($label, array_column($response[0]['values'], 0));
            if ($index !== false) {
                break;
            }
        }
        
        // Jika ditemukan, ambil nilai "Operating Cash Flow"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 3);
            
            $valid_cash_flow_values = [];
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }
        
            // Ambil nilai ke-2 jika ada, jika tidak, kembalikan 0.00
            return $valid_cash_flow_values[0] ?? 0.00;
        }
        
        return 0.00; // Kembalikan 0.00 jika label tidak ditemukan
        
    }
    
    protected function cash_flow_five_year_ago($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/financials/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

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

        
        $index = array_search("Diluted EPS", array_column($response[0]['values'], 0));

        // Jika ditemukan, ambil nilai "Diluted EPS"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 1); 
            
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }

            return $valid_cash_flow_values;
        }
        return 0.00; 
    }

    protected function cash_flow_history($symbol)
    {
        
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $periode2 = time();
        $periode1 = strtotime('-3 years', $periode2);
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/history/?period1='.$periode1.'&period2='. $periode2;

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

        $response = $crawler->filter('[data-testid="history-table"] tbody tr')->each(function ($node) {
            $rowData = [];
            $node->filter('td')->each(function ($tdNode) use (&$rowData) {
                $rowData[] = $tdNode->text();
            });
            return $rowData;
        });

        return $response;
    }

    public function cash_flow($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/cash-flow/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

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

        
        $desired_labels = ["Cash Flows from Used in Operating Activities Direct", "Operating Cash Flow"];
        $index = false;
        
        // Cari indeks dari label yang diinginkan
        foreach ($desired_labels as $label) {
            $index = array_search($label, array_column($response[0]['values'], 0));
            if ($index !== false) {
                break;
            }
        }
        
        // Jika ditemukan, ambil nilai "Operating Cash Flow"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 1);
            
            $valid_cash_flow_values = [];
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }
        
            // Ambil nilai ke-2 jika ada, jika tidak, kembalikan 0.00
            return $valid_cash_flow_values[0] ?? 0.00;
        }
        
        return 0.00; // Kembalikan 0.00 jika label tidak ditemukan
        
    }

    public function free_cash_flow($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }

        $url = 'https://finance.yahoo.com/quote/' . $symbol . '/cash-flow/';

        $crawler_service = new CrawlerService;
        $crawler = $crawler_service->main($url);

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

        
        $desired_labels = ["Free Cash Flow"];
        $index = false;
        
        // Cari indeks dari label yang diinginkan
        foreach ($desired_labels as $label) {
            $index = array_search($label, array_column($response[0]['values'], 0));
            if ($index !== false) {
                break;
            }
        }
        
        // Jika ditemukan, ambil nilai "Operating Cash Flow"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 1);
            
            $valid_cash_flow_values = [];
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }
        
            // Ambil nilai ke-2 jika ada, jika tidak, kembalikan 0.00
            return $valid_cash_flow_values[0] ?? 0.00;
        }
        
        return 0.00; // Kembalikan 0.00 jika label tidak ditemukan
        
    }

    public function deviden($symbol)
    {
        if (is_null($symbol) || empty($symbol)) {
            throw new \InvalidArgumentException("Symbol cannot be null or empty");
        }
        
        $url = 'https://finance.yahoo.com/quote/' . $symbol . '?p=' . $symbol . '&tsrc=fin-srch';
        $crawler = $this->main($url);
    
        $response = $crawler->filter('div[data-testid="quote-statistics"] ul li')->each(function ($node) {
            return [
                'label' => $node->filter('span:nth-child(1)')->text(),
                'value' => $node->filter('span:nth-child(2)')->text()
            ];
        });
    
        $index = array_search("Forward Dividend & Yield", array_column($response, 'label'));
    
        // Jika ditemukan, ambil nilai "Forward Dividend & Yield"
        if ($index !== false) {
            $eps_value = $response[$index]['value'];
            return $eps_value;
        } else {
            throw new \Exception("Forward Dividend & Yield data not found.");
        }
    }
    
}
