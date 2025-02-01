-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 02:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `published_date` date NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `published_date`, `description`) VALUES
(1, 'The Hobbit', 'J.R.R. Tolkien', '1937-09-21', 'A young hobbit embarks on a journey to recover stolen treasure.'),
(2, 'Pride and Prejudice', 'Jane Austen', '1813-01-28', 'The romantic clash between Elizabeth Bennet and the proud Mr. Darcy.'),
(3, 'Dracula', 'Bram Stoker', '1897-05-26', 'The story of Count Dracula\'s attempt to move from Transylvania to England.'),
(4, 'The Da Vinci Code', 'Dan Brown', '2003-03-18', 'A symbologist uncovers a religious mystery hidden for centuries.'),
(5, 'Gone Girl', 'Gillian Flynn', '2012-06-05', 'A psychological thriller about a wife\'s mysterious disappearance.'),
(6, 'The Catcher in the Rye', 'J.D. Salinger', '1951-07-16', 'A young man\'s disillusionment with adulthood and the world around him.'),
(7, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', '1997-06-26', 'A young boy discovers he is a wizard and attends a magical school.'),
(8, 'To Kill a Mockingbird', 'Harper Lee', '1960-07-11', 'The story of a young girl’s coming-of-age amidst racial injustice.'),
(9, 'The Little Prince', 'Antoine de Saint-Exupéry', '1943-04-06', 'A young prince travels from planet to planet learning about life and love.'),
(10, 'The Book Thief', 'Markus Zusak', '2005-03-14', 'A young girl living in Nazi Germany finds solace in books during wartime.'),
(11, 'The Importance of Being Earnest', 'Oscar Wilde', '1895-02-14', 'A comedic play about mistaken identities and social conventions.'),
(12, '1984', 'George Orwell', '1949-06-08', 'A dystopian society under the control of an omnipresent government.'),
(13, 'Frankenstein', 'Mary Shelley', '1818-01-01', 'A scientist creates a monster, leading to tragic consequences.'),
(15, 'The Diary of a Young Girl', 'Anne Frank', '1947-06-25', 'The poignant diary of a Jewish girl hiding from the Nazis during WWII.'),
(16, 'The Road', 'Cormac McCarthy', '2006-09-26', 'A father and son journey through a post-apocalyptic world.'),
(17, 'The Fellowship of the Ring', 'J.R.R. Tolkien', '1954-07-29', 'The first book in the epic fantasy series about a quest to destroy a powerful ring.'),
(18, 'The Great Gatsby', 'F. Scott Fitzgerald', '1925-04-10', 'A tragic story of a man’s obsession with wealth and the American Dream.'),
(19, 'The Night Circus', 'Erin Morgenstern', '2011-09-13', 'A magical competition between two young illusionists unfolds in a mysterious circus.'),
(20, 'The Picture of Dorian Gray', 'Oscar Wilde', '1890-06-01', 'A man remains eternally youthful while his portrait ages, showing his soul\'s corruption.'),
(21, 'Wuthering Heights', 'Emily Brontë', '1847-12-01', 'A passionate and destructive love story set on the English moors.'),
(22, 'The Fault in Our Stars', 'John Green', '2012-01-10', 'A love story between two cancer-stricken teenagers.'),
(23, 'It', 'Stephen King', '1986-09-15', 'A terrifying story of a group of children confronting a malevolent entity.'),
(24, 'Big Little Lies', 'Liane Moriarty', '2014-07-29', 'A gripping story about lies, secrets, and deception.'),
(25, 'The Shining', 'Stephen King', '1977-01-28', 'A horror novel about isolation and madness in a haunted hotel.'),
(26, 'The Hunger Games', 'Suzanne Collins', '2008-09-14', 'A dystopian novel set in a world where children fight to the death for entertainment.'),
(27, 'The Handmaid’s Tale', 'Margaret Atwood', '1985-04-17', 'A dystopian narrative about a totalitarian regime that subjugates women.'),
(28, 'The Girl with the Dragon Tattoo', 'Stieg Larsson', '2005-08-01', 'A journalist and a hacker uncover a dark family secret.'),
(29, 'A Game of Thrones', 'George R.R. Martin', '1996-08-06', 'Noble families vie for control of the Iron Throne in a medieval fantasy setting.'),
(30, 'Brave New World', 'Aldous Huxley', '1932-08-31', 'A dystopian novel set in a future society driven by technology and pleasure.'),
(31, 'Murder on the Orient Express', 'Agatha Christie', '1934-01-01', 'A detective investigates a murder on a luxurious train.'),
(32, 'The Outsiders', 'S.E. Hinton', '1967-04-24', 'A coming-of-age story about two rival groups of teenagers.'),
(33, 'The Chronicles of Narnia: The Lion, the Witch and the Wardrobe', 'C.S. Lewis', '1950-10-16', 'Four siblings discover a magical land through a wardrobe.'),
(34, 'The Color Purple', 'Alice Walker', '1982-01-01', 'A tale of racial and gender oppression in the American South.'),
(35, 'Wicked', 'Gregory Maguire', '1995-02-22', 'A retelling of the classic tale of the Wicked Witch of the West.'),
(36, 'Fahrenheit 451', 'Ray Bradbury', '1953-10-19', 'In a dystopian future, books are banned and burned.'),
(37, 'The Secret Garden', 'Frances Hodgson Burnett', '1911-08-01', 'A young girl discovers a hidden garden and helps to bring it back to life.'),
(38, 'The Alchemist', 'Paulo Coelho', '1988-05-01', 'A young shepherd embarks on a journey to fulfill his personal legend.'),
(39, 'The Glass Castle', 'Jeannette Walls', '2005-01-01', 'A memoir about growing up in a dysfunctional family.'),
(40, 'The Maze Runner', 'James Dashner', '2009-10-06', 'A group of teenagers must navigate a maze to escape.'),
(41, 'Sherlock Holmes: A Study in Scarlet', 'Arthur Conan Doyle', '1887-11-01', 'The first Sherlock Holmes novel that introduces the iconic detective.'),
(42, 'The Hitchhiker’s Guide to the Galaxy', 'Douglas Adams', '1979-10-12', 'A comedic science fiction novel about an unwitting human\'s intergalactic travels.'),
(43, 'The Bell Jar', 'Sylvia Plath', '1963-01-01', 'A semi-autobiographical novel about a young woman’s descent into mental illness.'),
(44, 'The War of the Worlds', 'H.G. Wells', '1898-01-01', 'An alien invasion story that explores human survival and resilience.'),
(45, 'Lord of the Flies', 'William Golding', '1954-09-17', 'A group of boys stranded on an island descend into savagery and violence.'),
(46, 'The Godfather', 'Mario Puzo', '1969-03-10', 'A crime novel that focuses on the powerful Mafia family of Don Vito Corleone.'),
(47, 'The Kite Runner', 'Khaled Hosseini', '2003-05-29', 'A story of friendship and redemption set against the backdrop of Afghanistan\'s tumultuous history.'),
(48, 'Bridgerton: The Duke and I', 'Julia Quinn', '2000-01-01', 'In this tale of love and family, Daphne Bridgerton enters into a fake courtship with Simon Basset, the Duke of Hastings, which becomes unexpectedly real.'),
(49, 'Bridgerton: The Viscount Who Loved Me', 'Julia Quinn', '2000-05-01', 'Anthony Bridgerton, the second son, searches for a wife who can meet his needs, but unexpectedly falls for the woman he least expects.'),
(50, 'Bridgerton: An Offer From a Gentleman', 'Julia Quinn', '2001-01-01', 'The third book in the series, featuring Benedict Bridgerton and his pursuit of an unlikely love with Sophie Beckett.'),
(51, 'Bridgerton: Romancing Mister Bridgerton', 'Julia Quinn', '2002-01-01', 'The fourth book in the series, following Colin Bridgerton and his romantic journey with Penelope Featherington.'),
(52, 'Bridgerton: To Sir Phillip, With Love', 'Julia Quinn', '2003-01-01', 'The fifth book in the series, where Eloise Bridgerton falls for Sir Phillip Crane.'),
(53, 'Bridgerton: When He Was Wicked', 'Julia Quinn', '2004-01-01', 'The sixth book in the series, focusing on Francesca Bridgerton and her relationship with Michael Stirling.'),
(54, 'Bridgerton: It\'s In His Kiss', 'Julia Quinn', '2005-01-01', 'The seventh book in the series, telling the story of Hyacinth Bridgerton and her romance with Gareth St. Clair.'),
(55, 'Bridgerton: On the Way to the Wedding', 'Julia Quinn', '2006-01-01', 'The eighth and final book in the Bridgerton series, following Gregory Bridgerton and his journey with Lucy Abernathy.'),
(56, 'The Notebook', 'Nicholas Sparks', '1996-10-01', 'A romantic novel that tells the story of Noah and Allie, whose love endures despite challenges.'),
(57, 'The Tearsmith', 'Erin Doom', '2021-06-08', 'A dark fantasy novel about a healer who crafts tears to cure ailments, and the profound consequences that follow. The story weaves themes of love, sacrifice, and the cost of healing in a mysterious, magical world.'),
(58, 'To All the Boys I’ve Loved Before', 'Jenny Han', '2009-04-01', 'Lara Jean Covey\'s secret love letters are sent to all her past crushes, changing her life in unexpected ways.'),
(59, 'To All the Boys I’ve Loved Before: P.S. I Still Love You', 'Jenny Han', '2015-05-26', 'The sequel to *To All the Boys I’ve Loved Before*, where Lara Jean deals with her feelings for Peter and another unexpected boy from her past.'),
(60, 'To All the Boys I’ve Loved Before: Always and Forever, Lara Jean', 'Jenny Han', '2017-05-02', 'The final book in the trilogy, where Lara Jean faces her future after high school and deals with the challenges of growing up.');

-- --------------------------------------------------------

--
-- Table structure for table `book_genres`
--

CREATE TABLE `book_genres` (
  `book_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_genres`
--

INSERT INTO `book_genres` (`book_id`, `genre_id`) VALUES
(1, 1),
(1, 9),
(2, 2),
(2, 6),
(3, 3),
(3, 14),
(4, 4),
(4, 5),
(5, 4),
(5, 5),
(6, 6),
(6, 7),
(7, 1),
(7, 7),
(8, 6),
(8, 8),
(9, 1),
(9, 9),
(10, 10),
(10, 11),
(11, 11),
(11, 12),
(12, 6),
(12, 13),
(13, 3),
(13, 14),
(15, 14),
(15, 15),
(16, 8),
(16, 13),
(17, 1),
(17, 12),
(18, 6),
(18, 8),
(19, 1),
(19, 9),
(20, 5),
(20, 14),
(21, 3),
(21, 14),
(22, 2),
(22, 7),
(23, 3),
(23, 5),
(24, 5),
(24, 8),
(25, 3),
(25, 14),
(26, 7),
(26, 13),
(27, 5),
(27, 13),
(28, 4),
(28, 5),
(29, 1),
(29, 4),
(30, 6),
(30, 13),
(31, 4),
(31, 5),
(32, 7),
(32, 8),
(33, 1),
(33, 9),
(34, 6),
(34, 14),
(35, 1),
(35, 9),
(36, 6),
(36, 13),
(37, 7),
(37, 9),
(38, 1),
(38, 6),
(39, 14),
(39, 15),
(40, 7),
(40, 13),
(41, 4),
(41, 5),
(42, 1),
(42, 12),
(43, 6),
(43, 14),
(44, 1),
(44, 13),
(45, 7),
(45, 13),
(46, 5),
(46, 6),
(47, 6),
(47, 10),
(48, 1),
(48, 6),
(49, 1),
(49, 6),
(50, 1),
(50, 7),
(51, 1),
(51, 7),
(52, 1),
(52, 7),
(53, 1),
(53, 7),
(54, 1),
(54, 7),
(55, 1),
(55, 7),
(56, 2),
(56, 7),
(57, 1),
(57, 2),
(58, 2),
(58, 8),
(59, 2),
(59, 8),
(60, 2),
(60, 8);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(15, 'Autobiography/Memoir'),
(12, 'Comedy'),
(8, 'Contemporary Literature'),
(13, 'Dystopian Fiction'),
(16, 'Education'),
(9, 'Fairy Tale'),
(1, 'Fantasy'),
(14, 'Gothic Fiction'),
(10, 'Historical'),
(3, 'Horror'),
(6, 'Literary Fiction'),
(4, 'Mystery'),
(2, 'Romance'),
(11, 'Short Story'),
(5, 'Thriller'),
(7, 'Young Adult');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_genres`
--
ALTER TABLE `book_genres`
  ADD PRIMARY KEY (`book_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_genres`
--
ALTER TABLE `book_genres`
  ADD CONSTRAINT `book_genres_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
