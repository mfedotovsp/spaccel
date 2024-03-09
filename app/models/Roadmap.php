<?php


namespace app\models;

/**
 * Дорожная карта по сегменту проекта
 *
 * Class Roadmap
 * @package app\models
 */
class Roadmap extends PropertyContainer
{

    /**
     * Roadmap constructor.
     * @param int $id
     * @param array $config
     */
    public function __construct(int $id, array $config = [])
    {
        /** @var $segment Segments */
        $segment = Segments::find(false)
            ->andWhere(['id' => $id])
            ->one();

        /** @var $confirm_segment Segments */
        $confirm_segment = Segments::find(false)
            ->andWhere(['id' => $id, 'exist_confirm' =>  1])
            ->one();

        /**
         * @var Problems $last_gps
         * @var Problems $first_confirm_gps
         */
        if ($confirm_segment) {
            /** @var $confirmSegment ConfirmSegment */
            $confirmSegment = ConfirmSegment::find(false)
                ->andWhere(['segment_id' => $segment->getId()])
                ->one();

            $last_gps = Problems::find(false)->andWhere(['basic_confirm_id' => $confirmSegment->getId()])->orderBy(['created_at' => SORT_DESC])->one();
            $first_confirm_gps = Problems::find(false)->andWhere(['basic_confirm_id' => $confirmSegment->getId(), 'exist_confirm' => 1])->andWhere(['not', ['time_confirm' => null]])->orderBy(['time_confirm' => SORT_ASC])->one();
        }

        /**
         * @var Gcps $last_gcp
         * @var Gcps $first_confirm_gcp
        */
        $last_gcp = Gcps::find(false)->andWhere(['segment_id' => $segment->getId()])->orderBy(['created_at' => SORT_DESC])->one();
        $first_confirm_gcp = Gcps::find(false)->andWhere(['segment_id' => $segment->getId(), 'exist_confirm' => 1])->andWhere(['not', ['time_confirm' => null]])->orderBy(['time_confirm' => SORT_ASC])->one();

        /**
         * @var Mvps $last_mvp
         * @var Mvps $first_confirm_mvp
        */
        $last_mvp = Mvps::find(false)->andWhere(['segment_id' => $segment->getId()])->orderBy(['created_at' => SORT_DESC])->one();
        $first_confirm_mvp = Mvps::find(false)->andWhere(['segment_id' => $segment->getId(), 'exist_confirm' => 1])->andWhere(['not', ['time_confirm' => null]])->orderBy(['time_confirm' => SORT_ASC])->one();

        $this->addProperty('created_at', $segment->getCreatedAt());
        $this->addProperty('plan_segment_confirm', ($segment->getCreatedAt() + 3600*24*10));
        $this->addProperty('plan_gps', ($segment->getCreatedAt() + 3600*24*20));
        $this->addProperty('plan_gps_confirm', ($segment->getCreatedAt() + 3600*24*30));
        $this->addProperty('plan_gcp', ($segment->getCreatedAt() + 3600*24*40));
        $this->addProperty('plan_gcp_confirm', ($segment->getCreatedAt() + 3600*24*50));
        $this->addProperty('plan_mvp', ($segment->getCreatedAt() + 3600*24*60));
        $this->addProperty('plan_mvp_confirm', ($segment->getCreatedAt() + 3600*24*70));

        $confirm_segment ? $this->addProperty('fact_segment_confirm', $confirm_segment->getTimeConfirm()) : $this->addProperty('fact_segment_confirm', null);
        $last_gps ? $this->addProperty('fact_gps', $last_gps->getCreatedAt()) : $this->addProperty('fact_gps', null);
        $first_confirm_gps ? $this->addProperty('fact_gps_confirm', $first_confirm_gps->getTimeConfirm()) : $this->addProperty('fact_gps_confirm', null);
        $last_gcp ? $this->addProperty('fact_gcp', $last_gcp->getCreatedAt()) : $this->addProperty('fact_gcp', null);
        $first_confirm_gcp ? $this->addProperty('fact_gcp_confirm', $first_confirm_gcp->getTimeConfirm()) : $this->addProperty('fact_gcp_confirm', null);
        $last_mvp ? $this->addProperty('fact_mvp', $last_mvp->getCreatedAt()) : $this->addProperty('fact_mvp', null);
        $first_confirm_mvp ? $this->addProperty('fact_mvp_confirm', $first_confirm_mvp->getTimeConfirm()) : $this->addProperty('fact_mvp_confirm', null);

        /** @var $confirmSegment ConfirmSegment */
        $confirmSegment = ConfirmSegment::find(false)
            ->andWhere(['segment_id' => $segment->getId()])
            ->one();

        $confirmSegment ? $this->addProperty('id_confirm_segment', $confirmSegment->getId()) : $this->addProperty('id_confirm_segment', null);
        $last_gps ? $this->addProperty('id_page_last_problem', $last_gps->getConfirmSegmentId()) : $this->addProperty('id_page_last_problem', null);

        /** @var $firstConfirmProblem ConfirmProblem */
        $firstConfirmProblem = !$first_confirm_gps ? false : ConfirmProblem::find(false)
            ->andWhere(['problem_id' => $first_confirm_gps->getId()])
            ->one();

        $firstConfirmProblem ? $this->addProperty('id_confirm_problem', $firstConfirmProblem->getId()) : $this->addProperty('id_confirm_problem', null);
        $last_gcp ? $this->addProperty('id_page_last_gcp', $last_gcp->getConfirmProblemId()) : $this->addProperty('id_page_last_gcp', null);

        /** @var $firstConfirmGcp ConfirmGcp */
        $firstConfirmGcp = !$first_confirm_gcp ? false : ConfirmGcp::find(false)
            ->andWhere(['gcp_id' => $first_confirm_gcp->getId()])
            ->one();

        $firstConfirmGcp ? $this->addProperty('id_confirm_gcp', $firstConfirmGcp->getId()) : $this->addProperty('id_confirm_gcp', null);
        $last_mvp ? $this->addProperty('id_page_last_mvp', $last_mvp->getConfirmGcpId()) : $this->addProperty('id_page_last_mvp', null);

        /** @var $firstConfirmMvp ConfirmMvp */
        $firstConfirmMvp = !$first_confirm_mvp ? false : ConfirmMvp::find(false)
            ->andWhere(['mvp_id' => $first_confirm_mvp->getId()])
            ->one();

        $firstConfirmMvp ? $this->addProperty('id_confirm_mvp', $firstConfirmMvp->getId()) : $this->addProperty('id_confirm_mvp', null);
        $this->addProperty('segment_name', $segment->getName());

        parent::__construct($config);
    }

}