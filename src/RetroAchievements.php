<?php

declare(strict_types=1);

namespace RetroAchievements\Api;

use GuzzleHttp\Client;

class RetroAchievements
{
    use MakesHttpRequests;

    public const BASE_URI = 'https://retroachievements.org';

    public const API_VERSION = 1;

    protected Client $client;

    public function __construct(public string $username, public string $apiKey, string $baseUri = self::BASE_URI)
    {
        $this->client = new Client([
            'base_uri' => $baseUri,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTopTenUsers(): mixed
    {
        return $this->getApiUrl('API_GetTopTenUsers.php');
    }

    public function getGameInfo(int $gameID): mixed
    {
        return $this->getApiUrl('API_GetGame.php', [
            'i' => $gameID,
        ]);
    }

    public function getGameInfoExtended(int $gameID): mixed
    {
        return $this->getApiUrl('API_GetGameExtended.php', [
            'i' => $gameID,
        ]);
    }

    public function getConsoleIDs(): mixed
    {
        return $this->getApiUrl('API_GetConsoleIDs.php');
    }

    public function getGameList(int $consoleID): mixed
    {
        return $this->getApiUrl('API_GetGameList.php', [
            'i' => $consoleID,
        ]);
    }

    public function getUserRankAndScore(string $user): mixed
    {
        return $this->getApiUrl('API_GetUserRankAndScore.php', [
            'u' => $user,
        ]);
    }

    public function getUserProgress(string $user, string $gameIDCSV): mixed
    {
        $gameIDCSV = preg_replace('/\s+/', '', $gameIDCSV); // Remove all whitespace

        return $this->getApiUrl('API_GetUserProgress.php', [
            'u' => $user,
            'i' => $gameIDCSV,
        ]);
    }

    public function getUserRecentlyPlayedGames(string $user, int $count, int $offset = 0): mixed
    {
        return $this->getApiUrl('API_GetUserRecentlyPlayedGames.php', [
            'u' => $user,
            'c' => $count,
            'o' => $offset,
        ]);
    }

    public function getUserSummary(string $user, int $numRecentGames): mixed
    {
        return $this->getApiUrl('API_GetUserSummary.php', [
            'u' => $user,
            'g' => $numRecentGames,
            'a' => 5,
        ]);
    }

    public function getGameInfoAndUserProgress(string $user, int $gameID): mixed
    {
        return $this->getApiUrl('API_GetGameInfoAndUserProgress.php', [
            'u' => $user,
            'g' => $gameID,
        ]);
    }

    public function getAchievementsEarnedOnDay(string $user, string $date): mixed
    {
        return $this->getApiUrl('API_GetAchievementsEarnedOnDay.php', [
            'u' => $user,
            'd' => $date,
        ]);
    }

    public function getAchievementsEarnedBetween(string $user, string $startDate, string $endDate): mixed
    {
        return $this->getApiUrl('API_GetAchievementsEarnedBetween.php', [
            'u' => $user,
            'f' => strtotime($startDate),
            't' => strtotime($endDate),
        ]);
    }

    protected function getApiUrl(string $endpoint, array $parameters = []): mixed
    {
        return $this->get(
            sprintf('/API/%s?%s', $endpoint, http_build_query([
                'z' => $this->username,
                'y' => $this->apiKey,
            ] + $parameters))
        );
    }
}
