<?php

namespace App\Controller\Api;

use \App\Model\Entity\Users\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class Psychologist extends Api
{


    private static function getPsychologistsItem($request, &$obPagination)
    {
        //Psicólogos
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTRO
        $totalLength = EntityUser::getUsers('user_type_id = 3', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //QUANTIDADE POR PÁGINA
        $qtdPagina = $queryParams['results'] ?? 5;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($totalLength, $paginaAtual, $qtdPagina);

        //RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers('user_type_id = 3 AND id <> ' . $request->user->id, 'name ASC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obUser = $results->fetchObject(EntityUser::class)) {
            //USUÁRIOS
            $itens[] = [
                'id' => $obUser->id,
                'crp' => $obUser->crp,
                'name' => $obUser->name,
                'email' => $obUser->email,
                'phone' => $obUser->phone1,
                'profile_image' => $obUser->profile_image,
            ];
        }

        //RETORNA OS USUÁRIOS DA LISTA
        return $itens;
    }

    public static function getPsychologists($request)
    {
        return Api::getApiResponse('Successful return to psychologist list', [
            'psychologists' => [
                [
                    'id' => 59,
                    'crp' => "06/45210",
                    'name' => 'Dr. Rafael Mendes',
                    'rating' => 4.7,
                    'specialization' => 'cognitive_behavioral',
                    'description' => 'Psicólogo especializado em terapia cognitivo-comportamental, com foco em ansiedade, depressão e desenvolvimento pessoal.',
                    'career_experience' => 7,
                    'posts' => 12,
                    'photo_url' => 'https://avatar.iran.liara.run/public?username=rafael_mendes'
                ],
                [
                    'id' => 60,
                    'crp' => "02/23488",
                    'name' => 'Dra. Larissa Fontes',
                    'rating' => 4.8,
                    'specialization' => 'family_therapy',
                    'description' => 'Atuação em mediação de conflitos familiares e acompanhamento de casais. Atendimento humanizado e escuta ativa.',
                    'career_experience' => 10,
                    'posts' => 8,
                    'photo_url' => 'https://avatar.iran.liara.run/public?username=larissa_fontes'
                ],
                [
                    'id' => 61,
                    'crp' => "05/12376",
                    'name' => 'Dr. Felipe Andrade',
                    'rating' => 4.6,
                    'specialization' => 'clinical_psychology',
                    'description' => 'Psicólogo clínico com foco em transtornos de humor e desenvolvimento da autoestima em jovens e adultos.',
                    'career_experience' => 5,
                    'posts' => 15,
                    'photo_url' => 'https://avatar.iran.liara.run/public?username=felipe_andrade'
                ],
                [
                    'id' => 62,
                    'crp' => "03/38751",
                    'name' => 'Dra. Renata Luz',
                    'rating' => 5.0,
                    'specialization' => 'adolescent_psychology',
                    'description' => 'Especialista no acompanhamento de adolescentes em fase escolar e orientação vocacional.',
                    'career_experience' => 6,
                    'posts' => 10,
                    'photo_url' => 'https://avatar.iran.liara.run/public?username=renata_luz'
                ],
                [
                    'id' => 63,
                    'crp' => "07/56432",
                    'name' => 'Dr. Tiago Ribeiro',
                    'rating' => 4.5,
                    'specialization' => 'psychoanalysis',
                    'description' => 'Trabalha com escuta psicanalítica em casos de sofrimento emocional crônico, luto e identidade.',
                    'career_experience' => 12,
                    'posts' => 18,
                    'photo_url' => 'https://avatar.iran.liara.run/public?username=tiago_ribeiro'
                ]
            ]
        ]);
    }
}
