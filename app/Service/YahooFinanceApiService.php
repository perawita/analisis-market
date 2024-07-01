<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

use DateTime;

class YahooFinanceApiService extends CrawlerService
{

    public function live_price($symbol)
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
            return (float)$basic_eps_values[0];
        }
    }

    public function eps_five_year_ago($symbol)
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

    public function cash_flow_one_year_ago($symbol)
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

        
        $desired_labels = ["Operating Cash Flow", "Cash Flows from Used in Operating Activities Direct"];
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

    public function cash_flow_now($symbol)
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

        
        $desired_labels = ["Operating Cash Flow", "Cash Flows from Used in Operating Activities Direct"];
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
    
    public function cash_flow_five_year_ago($symbol)
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

        
        $index = array_search("Operating Cash Flow", array_column($response[0]['values'], 0));

        // Jika ditemukan, ambil nilai "Operating Cash Flow"
        if ($index !== false) {
            $operating_cash_flow = array_slice($response[0]['values'][$index], 1); 
            
            foreach ($operating_cash_flow as $cash_flow) {
                if ($cash_flow !== "--") {
                    $valid_cash_flow_values[] = $cash_flow;
                }
            }

            return $valid_cash_flow_values;
        }
    }

}
