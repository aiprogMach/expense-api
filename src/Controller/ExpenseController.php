<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ExpenseDTO;
use App\Entity\Expense;
use App\Exception\InvalidPropertyException;
use App\Repository\ExpenseRepository;
use App\Service\ExpenseCreator;
use App\Service\ExpenseLister;
use App\Service\ExpenseUpdater;
use App\Transformer\ExpenseToDTOTransformer;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class ExpenseController extends BaseController
{
    private ExpenseRepository $expenseRepository;

    private ExpenseLister $expenseLister;

    private ExpenseCreator $expenseCreator;

    private ExpenseUpdater $expenseUpdater;

    private ExpenseToDTOTransformer $expenseToDTOTransformer;

    private SerializerInterface $serializer;

    private LoggerInterface $logger;

    public function __construct(
        ExpenseRepository $expenseRepository,
        ExpenseLister $expenseLister,
        ExpenseCreator $expenseCreator,
        ExpenseUpdater $expenseUpdater,
        ExpenseToDTOTransformer $expenseToDTOTransformer,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ){
        $this->expenseRepository = $expenseRepository;
        $this->expenseLister = $expenseLister;
        $this->expenseCreator = $expenseCreator;
        $this->expenseUpdater = $expenseUpdater;
        $this->expenseToDTOTransformer = $expenseToDTOTransformer;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @Route("/expenses", name="expense_list", methods={"GET","HEAD"})]
     * @QueryParam(name="page", requirements="\d+", default="0", description="Page number")
     * @QueryParam(name="countPerPage", requirements="\d+", default="100", description="Count per page")
     */
    public function list(ParamFetcherInterface $paramFetcher)
    {
        $page = (int) $paramFetcher->get('page');
        $countPerPage = (int) $paramFetcher->get('countPerPage');

        try {
            $dtos = $this->expenseLister->list($page, $countPerPage);
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->error('An error occurred while listing expenses. Contact system admin.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->toJson($dtos, Response::HTTP_OK);
    }

    /**
     * @Route("/expenses/{expenseId}", name="expense_show", methods={"GET"})
     */
    public function show(int $expenseId, Request $request)
    {
        $expense = $this->expenseRepository->find($expenseId);

        if (!$expense instanceof Expense) {
            return $this->error('Expense not found.', Response::HTTP_NOT_FOUND);
        }

        return $this->toJson($this->expenseToDTOTransformer->transformOne($expense), Response::HTTP_OK);
    }

    /**
     * @Route("/expenses", name="expense_create", methods={"POST"})
     */
    public function create(Request $request)
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), ExpenseDTO::class, 'json');
            $expense = $this->expenseCreator->create($dto);

            return $this->toJson($this->expenseToDTOTransformer->transformOne($expense), Response::HTTP_CREATED);
        } catch (InvalidPropertyException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }  catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->error('Cannot create expense, contact system admin for support.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/expenses/{expenseId}", name="expense_update", methods={"PUT"})
     */
    public function update(int $expenseId, Request $request)
    {
        $expense = $this->expenseRepository->find($expenseId);

        if (!$expense instanceof Expense) {
            return $this->error('Expense not found.', Response::HTTP_NOT_FOUND);
        }

        try {
            $dto = $this->serializer->deserialize($request->getContent(), ExpenseDTO::class, 'json');
            $expense = $this->expenseUpdater->update($expense, $dto);

            return $this->toJson($this->expenseToDTOTransformer->transformOne($expense), Response::HTTP_OK);
        } catch (InvalidPropertyException $e) {
            return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }  catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->error('Cannot update expense, contact system admin for support.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/expenses/{expenseId}", name="expense_delete", methods={"DELETE"})
     */
    public function delete(int $expenseId)
    {
        $expense = $this->expenseRepository->find($expenseId);

        if (!$expense instanceof Expense) {
            return $this->error('Expense not found.', Response::HTTP_NOT_FOUND);
        }

        try {
            $this->expenseRepository->remove($expense);

            return $this->toJson(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            dd($e);;
            $this->logger->error($e->getMessage());

            return $this->error('Cannot delete expense, contact system admin for support.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}