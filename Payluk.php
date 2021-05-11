<?php

namespace ezumah\payluk;

class Payluk
{
    protected $secret_key;

    public function __construct($secret_key)
    {
        $this->secret_key = $secret_key;
    }


    private function curl($url, $use_post, $post_data=[])
    {
        $curl = curl_init();
        $headers = [
            "Authorization: Bearer $this->secret_key",
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        if($use_post)
        {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
        }
        //Modify this two lines to suit your needs
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public function verify($reference)
    {
        $url = "https://api.payluk.com/v1/invoice/verify?reference=".$reference;

        if ($url)
        {
            return json_decode($this->curl($url, FALSE));
        }
        return false;
    }


    public function createInvoice(array $products, $details)
    {
        $url = "https://api.payluk.com/v1/invoice/generate";

        if (count($products) > 0 && count($details) > 0)
        {
            $post_data = [
                'products'   => $products,
                'info'       => $details,
            ];

            if ($post_data)
            {
                $response = $this->curl($url, TRUE, $post_data);

                $data = json_decode($response);

                if($data && $data->status)
                {
                    return $data;
                }

            }
        }

        return false;
    }

}