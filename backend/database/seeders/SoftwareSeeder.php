<?php

namespace Database\Seeders;

use App\Models\Software;
use Illuminate\Database\Seeder;

class SoftwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $software = [
            [
                'name' => 'Google Chrome',
                'description' => 'Fast, secure web browser with built-in Google services',
                'size' => '85.2 MB',
                'category' => 'Browser',
                'website_url' => 'https://www.google.com/chrome/',
                'download_url' => 'https://dl.google.com/chrome/install/chrome_installer.exe',
                'file_name' => 'chrome_installer.exe',
            ],
            [
                'name' => 'Mozilla Firefox',
                'description' => 'Open-source web browser with privacy focus',
                'size' => '78.4 MB',
                'category' => 'Browser',
                'website_url' => 'https://www.mozilla.org/firefox/',
                'download_url' => 'https://download.mozilla.org/?product=firefox-latest&os=win64&lang=en-US',
                'file_name' => 'firefox_installer.exe',
            ],
            [
                'name' => 'Visual Studio Code',
                'description' => 'Lightweight but powerful source code editor',
                'size' => '95.1 MB',
                'category' => 'Utilities',
                'website_url' => 'https://code.visualstudio.com/',
                'download_url' => 'https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-user',
                'file_name' => 'vscode_installer.exe',
            ],
            [
                'name' => 'Spotify',
                'description' => 'Music streaming service with millions of songs',
                'size' => '112.3 MB',
                'category' => 'Media',
                'website_url' => 'https://www.spotify.com/',
                'download_url' => 'https://download.scdn.co/SpotifySetup.exe',
                'file_name' => 'spotify_installer.exe',
            ],
            [
                'name' => 'Discord',
                'description' => 'Voice, video and text communication service',
                'size' => '67.8 MB',
                'category' => 'Media',
                'website_url' => 'https://discord.com/',
                'download_url' => 'https://dl.discordapp.net/distro/app/stable/win/x64/DiscordSetup.exe',
                'file_name' => 'discord_installer.exe',
            ],
            [
                'name' => 'VLC Media Player',
                'description' => 'Free and open source cross-platform multimedia player',
                'size' => '45.2 MB',
                'category' => 'Media',
                'website_url' => 'https://www.videolan.org/vlc/',
                'download_url' => 'https://download.videolan.org/vlc/last/win64/vlc-3.0.18-win64.exe',
                'file_name' => 'vlc_installer.exe',
            ],
            [
                'name' => '7-Zip',
                'description' => 'File archiver with high compression ratio',
                'size' => '1.5 MB',
                'category' => 'Utilities',
                'website_url' => 'https://www.7-zip.org/',
                'download_url' => 'https://www.7-zip.org/a/7z2301-x64.exe',
                'file_name' => '7zip_installer.exe',
            ],
            [
                'name' => 'Steam',
                'description' => 'Digital distribution platform for PC gaming',
                'size' => '2.1 MB',
                'category' => 'Gaming',
                'website_url' => 'https://store.steampowered.com/',
                'download_url' => 'https://cdn.akamai.steamstatic.com/client/installer/SteamSetup.exe',
                'file_name' => 'steam_installer.exe',
            ],
            [
                'name' => 'Epic Games Launcher',
                'description' => 'Epic Games Store client and game launcher',
                'size' => '65.4 MB',
                'category' => 'Gaming',
                'website_url' => 'https://www.epicgames.com/store/',
                'download_url' => 'https://launcher-public-service-prod06.ol.epicgames.com/launcher/api/installer/download/EpicGamesLauncherInstaller.msi',
                'file_name' => 'epicgames_installer.msi',
            ],
            [
                'name' => 'OBS Studio',
                'description' => 'Free and open source software for video recording and live streaming',
                'size' => '112.7 MB',
                'category' => 'Media',
                'website_url' => 'https://obsproject.com/',
                'download_url' => 'https://cdn-fastly.obsproject.com/downloads/OBS-Studio-29.1.3-Full-Installer-x64.exe',
                'file_name' => 'obs_installer.exe',
            ],
            [
                'name' => 'WhatsApp Desktop',
                'description' => 'WhatsApp messaging client for desktop',
                'size' => '142.3 MB',
                'category' => 'Media',
                'website_url' => 'https://www.whatsapp.com/download/',
                'download_url' => 'https://web.whatsapp.com/desktop/windows/release/x64/WhatsAppSetup.exe',
                'file_name' => 'whatsapp_installer.exe',
            ],
            [
                'name' => 'Zoom',
                'description' => 'Video conferencing and communication software',
                'size' => '89.2 MB',
                'category' => 'Utilities',
                'website_url' => 'https://zoom.us/',
                'download_url' => 'https://zoom.us/client/latest/ZoomInstaller.exe',
                'file_name' => 'zoom_installer.exe',
            ],
            [
                'name' => 'Java Runtime Environment',
                'description' => 'Runtime environment for Java applications',
                'size' => '76.8 MB',
                'category' => 'Utilities',
                'website_url' => 'https://www.oracle.com/java/',
                'download_url' => 'https://javadl.oracle.com/webapps/download/AutoDL?BundleId=248240_ce59cff5c23f4e2eaf4e778a117d4c5b',
                'file_name' => 'java_installer.exe',
            ],
            [
                'name' => 'Minecraft Launcher',
                'description' => 'Official launcher for Minecraft Java Edition',
                'size' => '4.2 MB',
                'category' => 'Gaming',
                'website_url' => 'https://www.minecraft.net/',
                'download_url' => 'https://launcher.mojang.com/download/MinecraftInstaller.msi',
                'file_name' => 'minecraft_installer.msi',
            ],
            [
                'name' => 'Notepad++',
                'description' => 'Free source code editor and text editor',
                'size' => '4.1 MB',
                'category' => 'Utilities',
                'website_url' => 'https://notepad-plus-plus.org/',
                'download_url' => 'https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.5.7/npp.8.5.7.Installer.x64.exe',
                'file_name' => 'notepadpp_installer.exe',
            ],
            [
                'name' => 'Git for Windows',
                'description' => 'Git version control system for Windows',
                'size' => '51.3 MB',
                'category' => 'Utilities',
                'website_url' => 'https://gitforwindows.org/',
                'download_url' => 'https://github.com/git-for-windows/git/releases/download/v2.42.0.windows.2/Git-2.42.0.2-64-bit.exe',
                'file_name' => 'git_installer.exe',
            ],
        ];

        foreach ($software as $item) {
            Software::create($item);
        }
    }
}
