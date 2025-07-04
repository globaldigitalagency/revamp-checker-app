<?php

namespace App\Controller;

use App\Form\RevampScanRequestFormType;
use App\Helper\SheetHelper;
use App\Messenger\Object\RevampScanRequest;
use App\Repository\RevampScanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class ScanController extends AbstractController
{
    const NUMBER_BY_PAGE = 9;

    public function __construct(
        private readonly string $projectDir,
        private readonly string $csvUploadDir,
        private readonly SheetHelper $sheetHelper,
        private readonly MessageBusInterface $bus,
        private readonly RevampScanRepository $revampScanRepository,
    ) {
    }

    public function index(int $page = 1): Response
    {
        $revampScans = $this->revampScanRepository->findAll();
        if (!empty($revampScans)) {
            $maxPage = ceil(count($revampScans) / self::NUMBER_BY_PAGE);
            if ($maxPage <= 0) {
                return $this->redirectToRoute('scan_index', [
                    'page' => 1,
                ]);
            } elseif ($page < 1 || $page > $maxPage) {
                return $this->redirectToRoute('scan_index', [
                    'page' => 1,
                ]);
            }
        }

        $revampScans = $this->revampScanRepository->getPaginatedScans($page, self::NUMBER_BY_PAGE);

        return $this->render('scan/index.html.twig', [
            'revampScans' => $revampScans,
            'maxPage' => $maxPage ?? 1,
            'page' => $page,
        ]);
    }

    public function new(Request $request): Response
    {
        $form = $this->createForm(RevampScanRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if (!empty($file)) {
                $file->move(sprintf('%s/%s', $this->projectDir, $this->csvUploadDir), $file->getClientOriginalName());

                $urls = $this->sheetHelper->getSheetData(
                    sprintf('%s/%s/%s', $this->projectDir, $this->csvUploadDir, $file->getClientOriginalName())
                );
            } else {
                $urls[] = $form->get('url')->getData();
            }

            foreach ($urls as $url) {
                if (
                    $url !== null
                    && preg_match(
                        '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)\/?$/mi',
                        $url
                    )
                ) {
                    $this->bus->dispatch(new RevampScanRequest($url));
                }
            }

            return $this->redirectToRoute('scan_index');
        }

        return $this->render('scan/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
